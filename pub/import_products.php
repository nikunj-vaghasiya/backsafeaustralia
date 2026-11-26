<?php
/**
 * Custom Product Import Script
 * Place this file in your Magento root directory
 * Run: php import_products.php
 */

error_reporting(E_ALL);
ini_set('display_errors', 1);

use Magento\Framework\App\Bootstrap;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Catalog\Model\ProductFactory;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Catalog\Model\Product\Attribute\Source\Status;

require __DIR__ . '/../app/bootstrap.php';

$bootstrap = Bootstrap::create(BP, $_SERVER);
$objectManager = $bootstrap->getObjectManager();

$state = $objectManager->get(\Magento\Framework\App\State::class);
$state->setAreaCode(\Magento\Framework\App\Area::AREA_ADMINHTML);

class ProductImporter
{
    const NEW_FLAG_YES = 1;
    const NEW_FLAG_NO = 0;
    const TAX_CLASS_ID = 2; // Taxable Goods
    const TAX_CLASS_ID_NONE = 0; // None
    const PRODUCT_ATTRIBUTE_SET_ID = 4; // Default attribute set
    const PRODUCT_VISIBILITY = 4; // Catalog, Search
    const SUPPLIER_ATTR_CODE = 'supplier';
    
    private $apiProductFactory;
    private $productRepository;
    private $productFactory;
    private $storeManager;
    private $eavConfig;
    private $attributeOptionManagement;
    private $optionFactory;
    private $resourceConnection;
    private $urlKeyHelper;
    
    public function __construct(
        $objectManager
    ) {
        // Initialize dependencies
        $this->apiProductFactory = $objectManager->get(\Integration\Product\Model\ProductFactory::class);
        $this->productRepository = $objectManager->get(ProductRepositoryInterface::class);
        $this->productFactory = $objectManager->get(ProductFactory::class);
        $this->storeManager = $objectManager->get(\Magento\Store\Model\StoreManagerInterface::class);
        $this->eavConfig = $objectManager->get(\Magento\Eav\Model\Config::class);
        $this->attributeOptionManagement = $objectManager->get(\Magento\Eav\Api\AttributeOptionManagementInterface::class);
        $this->optionFactory = $objectManager->get(\Magento\Eav\Api\Data\AttributeOptionInterfaceFactory::class);
        $this->resourceConnection = $objectManager->get(\Magento\Framework\App\ResourceConnection::class);
        $this->urlKeyHelper = $objectManager->get(\Magento\Catalog\Model\Product\Url::class);
    }
    
    public function execute($websiteId = 1, $storeId = 1, $totalNumber = 100)
    {
        try {
            echo "Starting product import...\n";
            
            // Get collection
            $apiProductCollection = $this->getApiProductCollection($websiteId, $totalNumber);
            
            $processed = 0;
            $success = 0;
            $failed = 0;
            
            foreach ($apiProductCollection as $apiProduct) {
                $processed++;
               // echo "\n[{$processed}] Processing SKU: " . $apiProduct->getSku() . "\n";
                
                try {
                    $this->addUpdateProduct($apiProduct, $storeId, $websiteId);
                    
                    // Update new_flag
                    //$apiProduct->setNewFlag(self::NEW_FLAG_YES)->save();
                    $success++;
                    
                } catch (\Exception $e) {
                    $failed++;
                    echo "ERROR: " . $e->getMessage() . "\n";
                    $this->log("Import product error for SKU => " . $apiProduct->getSku() . ': ' . $e->getMessage(), 'error.log');
                }
            }
            
            echo "\n=== Import Summary ===\n";
            echo "Total Processed: {$processed}\n";
            echo "Success: {$success}\n";
            echo "Failed: {$failed}\n";
            
        } catch (\Exception $e) {
            echo "FATAL ERROR: " . $e->getMessage() . "\n";
            echo $e->getTraceAsString() . "\n";
        }
    }
    
    protected function getApiProductCollection($websiteId, $totalNumber)
    {
        $apiProduct = $this->apiProductFactory->create();
        
        $collection = $apiProduct->getCollection()
            ->addFieldToFilter('new_flag', ['eq' => self::NEW_FLAG_YES])
            ->addFieldToFilter('website_id', $websiteId)
            ->setPageSize($totalNumber)
            ->setOrder('api_entity_id', 'DESC');
        
        echo "Found " . $collection->getSize() . " products to import\n";
        
        return $collection;
    }
    
    protected function addUpdateProduct($apiProduct, $storeId, $websiteId)
    {
        $sku = trim((string)$apiProduct->getSku());
        
        try {
            $product = $this->productRepository->get($sku, true, 0);
            $isUpdate = true;
        } catch (NoSuchEntityException $e) {
            $product = $this->productFactory->create();
            $isUpdate = false;
        }
        
        $taxClassId = (!empty($apiProduct->getTaxCategory()) && 
                      in_array(trim((string)$apiProduct->getTaxCategory()), $this->getApiGstFreeTaxCategories())) 
                      ? self::TAX_CLASS_ID_NONE 
                      : self::TAX_CLASS_ID;
        
        if ($isUpdate) {
            $apiProduct->setNewFlag(self::NEW_FLAG_NO)->save();
            // Update existing product
            //$this->updateProduct($product, $apiProduct, $taxClassId, $storeId);
            //echo "<pre>";print_r("Product updated - SKU: {$sku} - Product ID: " . $product->getId());echo "</pre>";
            $this->log("Product updated - SKU: {$sku} - Product ID: " . $product->getId(), 'success.log');
            
        } else {
            // Create new product
            //$this->createProduct($product, $apiProduct, $taxClassId, $storeId, $websiteId);
            echo "<pre>";print_r("New product created - SKU: {$sku} - Product ID: " . $product->getId());echo "</pre>";
            $this->log("New product created - SKU: {$sku} - Product ID: " . $product->getId(), 'success.log');
        }
    }
    
    protected function updateProduct($product, $apiProduct, $taxClassId, $storeId)
    {
        // Update product attributes directly
        $this->updateProductAttributes($product->getId(), $storeId, [
            'name' => $apiProduct->getName(),
            'tax_class_id' => $taxClassId
        ]);
        
        $product->setName($apiProduct->getName())
            ->setWeight($apiProduct->getWeight())
            ->setSupplier($this->getOrCreateAttributeOptionId(self::SUPPLIER_ATTR_CODE, $apiProduct->getSupplier()))
            ->setTaxClassId($taxClassId)
            ->setDescription($apiProduct->getDescription())
            ->setPrice($apiProduct->getPrice())
            ->setCategoryIds([102]);
        
        // Update stock
        $product->setStockData([
            'use_config_manage_stock' => 1,
            'qty' => $apiProduct->getQuantity(),
            'is_qty_decimal' => 0,
            'is_in_stock' => 1
        ]);
        
        $this->productRepository->save($product);
    }
    
    protected function createProduct($product, $apiProduct, $taxClassId, $storeId, $websiteId)
    {
        $urlName = $apiProduct->getName() . 'hubspot-';
        $sku = trim((string)$apiProduct->getSku());
        
        $product->setWebsiteIds([$websiteId])
            ->setStoreId($storeId)
            ->setName(trim((string)$apiProduct->getName()))
            ->setSku($sku)
            ->setTypeId(\Magento\Catalog\Model\Product\Type::TYPE_SIMPLE)
            ->setStatus(Status::STATUS_DISABLED)
            ->setDescription($apiProduct->getDescription())
            ->setCategoryIds([102])
            ->setAttributeSetId(self::PRODUCT_ATTRIBUTE_SET_ID)
            ->setVisibility(self::PRODUCT_VISIBILITY)
            ->setPrice($apiProduct->getPrice())
            ->setTaxClassId($taxClassId)
            ->setWeight($apiProduct->getWeight())
            ->setSupplier($this->getOrCreateAttributeOptionId(self::SUPPLIER_ATTR_CODE, $apiProduct->getSupplier()))
            ->setUrlKey($this->getUrlKey($urlName, $sku));
        
        // Set stock
        $product->setStockData([
            'use_config_manage_stock' => 1,
            'qty' => $apiProduct->getQuantity(),
            'is_qty_decimal' => 0,
            'is_in_stock' => 1
        ]);
        
        $this->productRepository->save($product);
       // $apiProduct->setNewFlag(self::NEW_FLAG_NO)->save();
    }
    
    protected function updateProductAttributes($productId, $storeId, $attributes)
    {
        $connection = $this->resourceConnection->getConnection();
        
        foreach ($attributes as $attributeCode => $value) {
            try {
                $attribute = $this->eavConfig->getAttribute('catalog_product', $attributeCode);
                if ($attribute && $attribute->getId()) {
                    $tableName = $attribute->getBackendTable();
                    
                    $connection->insertOnDuplicate(
                        $tableName,
                        [
                            'attribute_id' => $attribute->getId(),
                            'store_id' => $storeId,
                            'entity_id' => $productId,
                            'value' => $value
                        ],
                        ['value']
                    );
                }
            } catch (\Exception $e) {
                echo "Warning: Could not update attribute {$attributeCode}: " . $e->getMessage() . "\n";
            }
        }
    }
    
    protected function getOrCreateAttributeOptionId($attributeCode, $optionLabel)
    {
        if (empty($optionLabel)) {
            return null;
        }
        
        $attribute = $this->eavConfig->getAttribute('catalog_product', $attributeCode);
        
        // Check if option already exists
        $options = $attribute->getSource()->getAllOptions();
        foreach ($options as $option) {
            if (strtolower($option['label']) == strtolower($optionLabel)) {
                return $option['value'];
            }
        }
        
        // Create new option
        $option = $this->optionFactory->create();
        $option->setLabel($optionLabel);
        $option->setSortOrder(0);
        $option->setIsDefault(false);
        
        $this->attributeOptionManagement->add(
            \Magento\Catalog\Model\Product::ENTITY,
            $attributeCode,
            $option
        );
        
        // Clear cache and get new option ID
        $this->eavConfig->clear();
        $attribute = $this->eavConfig->getAttribute('catalog_product', $attributeCode);
        $options = $attribute->getSource()->getAllOptions();
        
        foreach ($options as $option) {
            if (strtolower($option['label']) == strtolower($optionLabel)) {
                return $option['value'];
            }
        }
        
        return null;
    }
    
    protected function getUrlKey($name, $sku)
    {
        $urlKey = preg_replace('#[^0-9a-z]+#i', '-', strtolower($name));
        $urlKey = strtolower($urlKey);
        $urlKey = trim($urlKey, '-');
        
        // Check if URL key exists
        $connection = $this->resourceConnection->getConnection();
        $select = $connection->select()
            ->from($this->resourceConnection->getTableName('catalog_product_entity_varchar'), 'COUNT(*)')
            ->where('value = ?', $urlKey);
        
        $count = $connection->fetchOne($select);
        
        if ($count > 0) {
            $urlKey .= '-' . strtolower($sku);
        }
        
        return $urlKey;
    }
    
    protected function getApiGstFreeTaxCategories()
    {
        // Add your GST-free tax categories here
        return [];
    }
    
    protected function log($message, $filename)
    {
        $logDir = BP . '/var/log/product_import/';
        if (!is_dir($logDir)) {
            mkdir($logDir, 0755, true);
        }
        
        file_put_contents(
            $logDir . $filename,
            date('Y-m-d H:i:s') . ' - ' . $message . "\n",
            FILE_APPEND
        );
    }
}

// Execute the import
try {
    $importer = new ProductImporter($objectManager);
    
    // Parameters: websiteId, storeId, totalNumber
    $websiteId = 1;  // Change as needed
    $storeId = 1;    // Change as needed
    $totalNumber = 7901; // Number of products to process
    
    $importer->execute($websiteId, $storeId, $totalNumber);
    
} catch (\Exception $e) {
    echo "Script failed: " . $e->getMessage() . "\n";
    echo $e->getTraceAsString() . "\n";
}