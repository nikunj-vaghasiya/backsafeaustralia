<?php

namespace Integration\Product\Model\Import;

use Magento\Framework\Exception\NoSuchEntityException;
use Magento\ConfigurableProduct\Model\Product\Type\Configurable as ConfigurableType;
use Magento\Catalog\Model\Product\Attribute\Source\Status;
use Magento\InventoryApi\Api\Data\SourceItemInterface;
use Magento\InventoryApi\Api\Data\SourceItemInterfaceFactory;
use Magento\InventoryApi\Api\SourceItemsSaveInterface;

class Process
{
	const RECORD_LIMIT = 100;
	const NEW_FLAG_NO = 0;
	const NEW_FLAG_YES = 1;
	const PRODUCT_ATTRIBUTE_SET_ID = 4;
	const PRODUCT_VISIBILITY = 1;
	const TAX_CLASS_ID = 2; /* Taxable Goods */
	const TAX_CLASS_ID_NONE = 0; /* None */
	const STORE_ID = 0;
	const URL_SUFFIX = '.html';
	const BRAND_ATTR_CODE = 'pro_brand';
	const SIZE_ATTR_CODE = 'size';
	const COLOR_ATTR_CODE = 'color';
	const GENDER_ATTR_CODE = 'gender';
	const GLOVE_TYPE_ATTR_CODE = 'glove_type';
	const SUPPLIER_ATTR_CODE = 'supplier';
	const DEFAULT_PRODUCT_QTY = 10;

	protected $_apiGstFreeTaxCategories = ['OTHGSTFREE'];

	protected $_websiteId = 1; /* Main Website */

	protected $_storeId = 1; /* Default Store View */

	protected $_mode = 'auto';

	protected $_typeId = null;

	protected $_processTypeId = null;

	protected $_resource;
	protected $apiProduct;
	protected $cronBuffer;
	protected $_productUrl;
	protected $manualHelper;
	protected $_productModel;
	protected $_productAction;
	protected $_optionFactory;
	protected $manualSyncWatch;
	protected $intProductHelper;
	protected $_productRepository;
	protected $_connection;
	protected $_optionLabelFactory;
	protected $_attributeRepository;
	protected $_urlRewriteCollection;
	protected $_attributeOptionManagement;
	protected $sourceItemFactory;
	protected $sourceItemsSave;

	public function __construct(
		\Integration\Cron\Model\Buffer $cronBuffer,
		\Magento\Catalog\Model\Product $productModel,
		\Magento\Catalog\Model\Product\Url $productUrl,
		\Integration\Product\Model\Product $apiProduct,
		\Integration\ManualSync\Helper\Data $manualHelper,
		\Integration\Product\Helper\Data $intProductHelper,
		\Magento\Framework\App\ResourceConnection $resource,
		\Integration\ManualSync\Model\Watch $manualSyncWatch,
		\Magento\Catalog\Model\ResourceModel\Product\Action $productAction,
		\Magento\Catalog\Api\ProductRepositoryInterface $productRepository,
		\Magento\Eav\Api\Data\AttributeOptionInterfaceFactory $_optionFactory,
		\Magento\Catalog\Api\ProductAttributeRepositoryInterface $_attributeRepository,
		\Magento\Eav\Api\Data\AttributeOptionLabelInterfaceFactory $_optionLabelFactory,
		\Magento\Eav\Api\AttributeOptionManagementInterface $_attributeOptionManagement,
		\Magento\UrlRewrite\Model\ResourceModel\UrlRewriteCollection $urlRewriteCollection,
		\Magento\ConfigurableProduct\Model\ResourceModel\Product\Type\Configurable $configurableProduct,
		SourceItemInterfaceFactory $sourceItemFactory,
		SourceItemsSaveInterface $sourceItemsSave
	) {
		$this->_resource = $resource;
		$this->apiProduct = $apiProduct;
		$this->cronBuffer = $cronBuffer;
		$this->_productUrl = $productUrl;
		$this->manualHelper = $manualHelper;
		$this->_productModel = $productModel;
		$this->_productAction = $productAction;
		$this->_optionFactory = $_optionFactory;
		$this->manualSyncWatch = $manualSyncWatch;
		$this->intProductHelper = $intProductHelper;
		$this->_productRepository = $productRepository;
		$this->_connection = $resource->getConnection();
		$this->_optionLabelFactory = $_optionLabelFactory;
		$this->_attributeRepository = $_attributeRepository;
		$this->_urlRewriteCollection = $urlRewriteCollection;
		$this->_attributeOptionManagement = $_attributeOptionManagement;
		$this->sourceItemFactory = $sourceItemFactory;
		$this->sourceItemsSave = $sourceItemsSave;
	}

	/**
	 * Set website id for current import process
	 *
	 * @param int $websiteid
	 * @return \Integration\Product\Model\Import\Process
	 */
	public function setWebsiteId(int $websiteid)
	{
		$this->_websiteId = (int)$websiteid;

		return $this;
	}

	/**
	 * @return int $websiteId
	 */
	public function getWebsiteId()
	{
		return $this->_websiteId;
	}

	public function getStoreId()
	{
		if (!$this->getWebsiteId()) {
			return $this->_storeId;
		}

		return $this->intProductHelper->getStoreIdByWebsiteId($this->getWebsiteId());
	}

	public function setMode(string $mode)
	{
		$this->_mode = $mode;

		return $this;
	}

	public function getMode()
	{
		return $this->_mode;
	}

	public function setLogTypeId(int $typeId)
	{
		$this->_typeId = $typeId;

		return $this;
	}

	public function getLogTypeId()
	{
		if (is_null($this->_typeId)) {
			$this->setLogTypeId(\Integration\ManualSync\Helper\Data::PRODUCT_IMPORT_TYPE_ID);
		}

		return $this->_typeId;
	}

	public function setProcessTypeId(int $processTypeId)
	{
		$this->_processTypeId = $processTypeId;

		return $this;
	}

	public function getProcessTypeId()
	{
		if (is_null($this->_processTypeId)) {
			$this->setProcessTypeId(\Integration\ManualSync\Helper\Data::PRODUCT_DUMP_TYPE_ID);
		}

		return $this->_processTypeId;
	}

	/**
	 * Run process the import of products
	 * Default it will run for Main website
	 *
	 * @param int $websiteId
	 * @return \Integration\Product\Model\Import\Process
	 */
	public function runProcess($schedule = null)
	{
		$batch = $this->cronBuffer->getBatchRecord(
			$this->getProcessTypeId(),
			$this->getMode(),
			\Integration\ManualSync\Model\Status::SYNC_PENDING_STATUS,
			$this->getWebsiteId()
		);

		try {
			if ($batch && $batch->getId()) {
				$cronScheduleId = (!empty($schedule) && $schedule->getId()) ? $schedule->getId() : 'NULL';
				/* udate cron record*/
				$this->cronBuffer->setStatus(\Integration\ManualSync\Model\Status::SYNC_RUNNING_STATUS)->setStartAt($this->manualHelper->getCurrentDatetime())->setScheduleId($cronScheduleId)->save();
				$apiProductCollection = $this->_getDumpCollection($batch->getTotalNumber());
				if ($apiProductCollection && $apiProductCollection->count()) {
					/* Manual Sync Tracking */
					if ($this->getMode() == \Integration\ManualSync\Helper\Data::MANUAL_MODE) {
						$lastRunningRecord = $this->manualSyncWatch->getLastRunningRecord(
							$this->getLogTypeId(),
							\Integration\ManualSync\Model\Status::SYNC_RUNNING_STATUS,
							$this->getWebsiteId()
						);

						if ($lastRunningRecord && $lastRunningRecord->getId()) {
							$this->manualSyncWatch->setId($lastRunningRecord->getId())
								->setTotalNumber($batch->getTotalNumber())
								->save();
						}
					}
					/* Manual Sync Tracking */

					$startMonitorText = "Start Import(cron_buffer_id: " . $batch->getId() . ")=> " . $this->manualHelper->getMelbourneCurrentDateTime();
					$this->intProductHelper->createLogFiles($startMonitorText, 'INFO', $this->intProductHelper::PRODUCT_IMPORT_LOG, 'cron-buffer-monitor.log', $this->getWebsiteId());
					$this->intProductHelper->createLogFiles($startMonitorText, 'INFO', $this->intProductHelper::PRODUCT_IMPORT_LOG, 'success.log', $this->getWebsiteId());

					foreach ($apiProductCollection as $apiProduct) {
						$this->_addUpdateProduct($apiProduct);
						$apiProduct->setNewFlag(self::NEW_FLAG_NO)->save();
					}
					/* cron log */

					$endMonitorText = "End Import(cron_buffer_id: " . $batch->getId() . ")=> " . $this->manualHelper->getMelbourneCurrentDateTime();
					$this->intProductHelper->createLogFiles($endMonitorText, 'INFO', $this->intProductHelper::PRODUCT_IMPORT_LOG, 'cron-buffer-monitor.log', $this->getWebsiteId());
					$this->intProductHelper->createLogFiles($endMonitorText, 'INFO', $this->intProductHelper::PRODUCT_IMPORT_LOG, 'success.log', $this->getWebsiteId());
				} else {
					echo 'No records found for import' . "<br/>";
					$this->intProductHelper->createLogFiles('No Records found for import', 'INFO', $this->intProductHelper::PRODUCT_IMPORT_LOG, 'success.log', $this->getWebsiteId());
				}
				/* udate cron record*/
				$this->cronBuffer->setStatus(\Integration\ManualSync\Model\Status::SYNC_SUCCESS_STATUS)->setEndAt($this->manualHelper->getCurrentDatetime())->setNewFlag(self::NEW_FLAG_NO)->save();
				$this->intProductHelper->createLogFiles("After set success status flag: " . \Integration\ManualSync\Model\Status::SYNC_SUCCESS_STATUS, 'INFO', $this->intProductHelper::PRODUCT_IMPORT_LOG, 'cron-buffer-monitor.log', $this->getWebsiteId());
			} else {
				echo 'No batch found for import';
				$this->intProductHelper->createLogFiles('No batch found for import', 'INFO', $this->intProductHelper::PRODUCT_IMPORT_LOG, 'success.log', $this->getWebsiteId());
			}
		} catch (\Exception $e) {
			/* TO DO */
			$this->intProductHelper->createLogFiles('Import product error: ' . $e->getMessage(), 'CRIT', $this->intProductHelper::PRODUCT_IMPORT_LOG, 'error.log', $this->getWebsiteId());

			/* Manual Sync Tracking */
			if ($this->getMode() == \Integration\ManualSync\Helper\Data::MANUAL_MODE) {
				$lastRunningRecord = $this->manualSyncWatch->getLastRunningRecord(
					$this->getLogTypeId(),
					\Integration\ManualSync\Model\Status::SYNC_RUNNING_STATUS,
					$this->getWebsiteId()
				);

				if ($lastRunningRecord && $lastRunningRecord->getId()) {
					$this->manualSyncWatch->setId($lastRunningRecord->getId())->setStatus('error')->setErrorMessage($e->getMessage())->save();
				}
			}
			/* Manual Sync Tracking */
		}
		return $this;
	}

	/**
	 * Returns new dump products collection
	 *
	 * @return  \Integration\Product\Model\ResourceModel\Product\Collection $collection|false
	 */
	protected function _getDumpCollection($totalNumber = self::RECORD_LIMIT)
	{
		$collection = $this->apiProduct->getCollection()
			->addFieldToFilter('new_flag', ['eq' => self::NEW_FLAG_YES])
			->addFieldToFilter('website_id', $this->getWebsiteId())
			->setPageSize($totalNumber)
			->setOrder('api_entity_id', 'DESC');

		return $collection;
	}

	/**
	 * Import product in website
	 *
	 * @param \Integration\Product\Model\Product $apiProduct
	 * @return void
	 */
	protected function _addUpdateProduct(&$apiProduct)
	{
		try {
			try {
				$product = $this->_productRepository->get(trim((string)$apiProduct->getSku()), true, 0);
			} catch (NoSuchEntityException $e) {
				$product = $this->_productModel;
			}

			try {
				$taxClassId = (!empty($apiProduct->getTaxCategory()) && in_array(trim((string)$apiProduct->getTaxCategory()), $this->_apiGstFreeTaxCategories)) ? self::TAX_CLASS_ID_NONE : self::TAX_CLASS_ID;

				if (!empty($product) && $product->getId()) {
					/* START: Due to use default checkbox check/uncheck values are not update sometimes so update attributes like price and status additionally */
					$productData = [
						'name' => $apiProduct->getName(),
						'tax_class_id' => $taxClassId/*,
						'price' => $apiProduct->getPrice()*/
					];

					$this->_updateProductAttributes($product->getId(), $this->getStoreId(), $productData);
					/* END */
					$product->setName($apiProduct->getName())
						->setWeight($apiProduct->getWeight())
						->setSupplier($this->getOrCreateAttributeOptionId(self::SUPPLIER_ATTR_CODE, $apiProduct->getsupplier()))
						->setTaxClassId($taxClassId)
						/*->setDescription($apiProduct->getDescription())*/
						->setPrice($apiProduct->getPrice())
						/*->setCategoryIds([102])*/
						/*->setStockData([
							'use_config_manage_stock' => 1,
							'qty' => $apiProduct->getQuantity(),
							'is_qty_decimal' => 0,
							'is_in_stock' => 1
						])*/
						->save();

					$this->updateSourceItemQty(
					    trim((string)$apiProduct->getSku()),
					    self::DEFAULT_PRODUCT_QTY
					);

					$message = 'Product updated - SKU => ' . trim((string)$apiProduct->getSku()) . ' - Web Product ID: ' . $product->getId();
					echo $message;
					echo "<br>";
					$this->intProductHelper->createLogFiles($message, 'INFO', $this->intProductHelper::PRODUCT_IMPORT_LOG, 'success.log', $this->getWebsiteId());
					$product->unsetData();
				} else {
					try {
						$url_name = $apiProduct->getName().'hubspot-';
						$status = Status::STATUS_DISABLED;
						$product->setWebsiteIds([$this->getWebsiteId()])
							->setStoreId($this->getStoreId())
							->setName(trim((string)$apiProduct->getName()))
							->setSku(trim((string)$apiProduct->getSku()))
							->setTypeId(\Magento\Catalog\Model\Product\Type::TYPE_SIMPLE)
							->setStatus($status)
							->setDescription($apiProduct->getDescription())
							->setCategoryIds([102])
							->setAttributeSetId(self::PRODUCT_ATTRIBUTE_SET_ID)
							->setVisibility(self::PRODUCT_VISIBILITY)
							->setPrice($apiProduct->getPrice())
							->setTaxClassId($taxClassId)
							->setStockData([
								'use_config_manage_stock' => 1,
								'qty' => self::DEFAULT_PRODUCT_QTY,
								'is_qty_decimal' => 0,
								'is_in_stock' => 1
							])
							->setWeight($apiProduct->getWeight())
							->setSupplier($this->getOrCreateAttributeOptionId(self::SUPPLIER_ATTR_CODE, $apiProduct->getsupplier()))
							->setUrlKey($this->getUrlKey($url_name, trim((string)$apiProduct->getSku())))
							->save();

						$this->updateSourceItemQty(
						    trim((string)$apiProduct->getSku()),
						    self::DEFAULT_PRODUCT_QTY,
						);

						$message = 'New product created - SKU => ' . trim((string)$apiProduct->getSku());
						echo $message;
						echo "<br>";
						$this->intProductHelper->createLogFiles($message, 'INFO', $this->intProductHelper::PRODUCT_IMPORT_LOG, 'success.log', $this->getWebsiteId());
						$product->unsetData();
					} catch (\Exception $e) {
						echo "<pre>";print_r($e->getMessage());die;
					}
				}
			} catch (\Exception $e) {
				$this->intProductHelper->createLogFiles('Import product error for SKU => ' . $apiProduct->getSku() . ': ' . $e->getMessage(), 'CRIT', $this->intProductHelper::PRODUCT_IMPORT_LOG, 'error.log', $this->getWebsiteId());
				return '';
			}
		} catch (\Exception $e) {
			/* TO DO */
			$this->intProductHelper->createLogFiles('Import product error for SKU => ' . $apiProduct->getSku() . ': ' . $e->getMessage(), 'CRIT', $this->intProductHelper::PRODUCT_IMPORT_LOG, 'error.log', $this->getWebsiteId());
		}
	}

	protected function _updateProductAttributes($productId, $storeId, $productData)
	{
		if ($productId && is_array($productData)) {
			$validKey = ['status', 'name', 'price', 'special_price', 'weight', 'brand', 'tax_class_id'];

			$isValidProductData = [];
			if (count($productData)) {
				foreach ($productData as $key => $value) {
					if (in_array($key, $validKey) && ($value != "" || !is_null($value))) {
						$isValidProductData[$key] = $value;
					}
				}
			}

			if (count($isValidProductData)) {
				$productId = (!is_array($productId)) ? [$productId] : $productId;
				$this->_productAction->updateAttributes($productId, $isValidProductData, $storeId);
			}
		}

		return $this;
	}

	public function getUrlKey($name, $sku = '')
	{
		try {
			$urlKey = $this->_productUrl->formatUrlKey($name);
			if ($sku != '') {
				/*$products = $this->_productModel->getCollection()->addAttributeToFilter('status', ['in' => [1,2]])->addAttributeToFilter('url_key',trim($urlKey));

				$count = count($products);
				if($count == 0){
					$searchKey = $urlKey.self::URL_SUFFIX;
					$rewriteCollection = $this->_urlRewriteCollection->addFieldToFilter('request_path',trim($searchKey));
					$count = count($rewriteCollection);
				}

				if($count >= 1){*/
				$urlKey = $urlKey . '-' . str_replace(" ", "-", $sku);
				/*}*/
			}
			$this->intProductHelper->createLogFiles('Import product url sku: ' . $sku . ' Url Key: ' . $urlKey, 'INFO', $this->intProductHelper::PRODUCT_IMPORT_LOG, 'url.log', $this->getWebsiteId());
			return $urlKey;
		} catch (\Exception $e) {
			$this->intProductHelper->createLogFiles('Import product url key error: Name => ' . $name . 'Sku => ' . $sku . ' Url Key => ' . $urlKey . ' error message => ' . $e->getMessage(), 'CRIT', $this->intProductHelper::PRODUCT_IMPORT_LOG, 'url.log', $this->getWebsiteId());
			return '';
		}
	}

	public function getAttribute($attributeCode)
	{
		return $this->_attributeRepository->get($attributeCode);
	}

	public function getAttributeOptionId($attributeCode, $label)
	{
		$label = trim((string)$label);
		try {
			$attribute = $this->getAttribute($attributeCode);
			$id = $attribute->getSource()->getOptionId($label);
			if ($id && $id > 0) {
				return $id;
			}
			return false;
		} catch (\Exception $e) {
			$this->intProductHelper->createLogFiles('Attribute label id Error => ' . $attributeCode . ' || ' . $label . "\n" . $e->getMessage(), 'CRIT', $this->intProductHelper::PRODUCT_IMPORT_LOG, 'error.log', $this->getWebsiteId());
			return false;
		}
	}

	public function getOrCreateAttributeOptionId($attributeCode, $label)
	{
		$label = trim((string)$label);
		try {
			if ($label != '') {
				/* Does Attribute Option already exist? */
				$optionId = $this->getAttributeOptionId($attributeCode, $label);

				if (!$optionId) {
					/* Add Attribute Option if not exists */
					$optionLabel = $this->_optionLabelFactory->create();
					$optionLabel->setStoreId(self::STORE_ID);
					$optionLabel->setLabel($label);

					$option = $this->_optionFactory->create();
					$option->setLabel($optionLabel->getLabel());
					$option->setStoreLabels([$optionLabel]);
					$option->setSortOrder(0);
					$option->setIsDefault(false);

					$this->_attributeOptionManagement->add(
						\Magento\Catalog\Model\Product::ENTITY,
						$this->getAttribute($attributeCode)->getAttributeId(),
						$option
					);
					$optionId = $this->getAttributeOptionId($attributeCode, $label, true);
				}
				return $optionId;
			}
			return '';
		} catch (\Exception $e) {
			$this->intProductHelper->createLogFiles('Add or get Attribute option Error => ' . $attributeCode . ' || ' . $label . "\n" . $e->getMessage(), 'CRIT', $this->intProductHelper::PRODUCT_IMPORT_LOG, 'error.log', $this->getWebsiteId());
			return '';
		}
	}

	protected function updateSourceItemQty($sku, $qty)
	{
	    try {
	        $sku = trim((string)$sku);
	        $qty = (float)$qty;

	        if ($sku === '') {
	            return false;
	        }

	        /**
	         * Magento default source.
	         *
	         * If your Magento installation uses a custom source,
	         * replace 'default' with the appropriate source code.
	         */
	        $sourceItem = $this->sourceItemFactory->create();

	        $sourceItem->setSourceCode('default');
	        $sourceItem->setSku($sku);
	        $sourceItem->setQuantity($qty);

	        /**
	         * Set MSI stock status based on quantity.
	         */
	        if ($qty > 0) {
	            $sourceItem->setStatus(
	                SourceItemInterface::STATUS_IN_STOCK
	            );
	        } else {
	            $sourceItem->setStatus(
	                SourceItemInterface::STATUS_OUT_OF_STOCK
	            );
	        }

	        /**
	         * Save source item through Magento MSI service contract.
	         *
	         * This will allow Magento's inventory system to
	         * recalculate salable quantity.
	         */
	        $this->sourceItemsSave->execute([$sourceItem]);

	        $message = 'MSI source quantity updated - SKU => ' . $sku .
	            ' - Quantity => ' . $qty .
	            ' - Source => default';

	        $this->intProductHelper->createLogFiles(
	            $message,
	            'INFO',
	            $this->intProductHelper::PRODUCT_IMPORT_LOG,
	            'success.log',
	            $this->getWebsiteId()
	        );

	        return true;

	    } catch (\Exception $e) {

	        $this->intProductHelper->createLogFiles(
	            'MSI source quantity update error - SKU => ' . $sku .
	            ' - Quantity => ' . $qty .
	            ' - Error => ' . $e->getMessage(),
	            'CRIT',
	            $this->intProductHelper::PRODUCT_IMPORT_LOG,
	            'error.log',
	            $this->getWebsiteId()
	        );

	        return false;
	    }
	}
}
