<?php
/**
 * Optimized script using direct SQL queries for bulk update
 * Better performance for large product catalogs
 * Compatible with Magento 2.4.8-p2
 * 
 * Usage: php update_simple_products_stock_optimized.php
 */

use Magento\Framework\App\Bootstrap;
use Magento\Catalog\Model\Product\Type;

require_once __DIR__ . '/../app/bootstrap.php';

$bootstrap = Bootstrap::create(BP, $_SERVER);
$objectManager = $bootstrap->getObjectManager();

$state = $objectManager->get(\Magento\Framework\App\State::class);
$state->setAreaCode(\Magento\Framework\App\Area::AREA_ADMINHTML);

// Get resource connection
$resource = $objectManager->get(\Magento\Framework\App\ResourceConnection::class);
$connection = $resource->getConnection();

// Configuration
$newQty = 10;
$isInStock = 1; // 1 = In Stock, 0 = Out of Stock

try {
    echo "Starting optimized bulk update for simple products...\n\n";
    
    // Get table names
    $catalogProductEntityTable = $resource->getTableName('catalog_product_entity');
    $catalogInventoryStockItemTable = $resource->getTableName('cataloginventory_stock_item');
    
    // Get all simple product IDs
    $select = $connection->select()
        ->from($catalogProductEntityTable, ['entity_id', 'sku'])
        ->where('type_id = ?', Type::TYPE_SIMPLE);
    
    $simpleProducts = $connection->fetchAll($select);
    $totalProducts = count($simpleProducts);
    
    echo "Found {$totalProducts} simple products\n";
    echo "Updating stock quantities and status...\n\n";
    
    if ($totalProducts > 0) {
        $productIds = array_column($simpleProducts, 'entity_id');
        
        // Update stock items in bulk
        $updated = $connection->update(
            $catalogInventoryStockItemTable,
            [
                'qty' => $newQty,
                'is_in_stock' => $isInStock
            ],
            [
                'product_id IN (?)' => $productIds
            ]
        );
        
        echo "✓ Successfully updated {$updated} stock records\n\n";
        
        // Display sample of updated products
        echo "Sample of updated products:\n";
        echo "----------------------------\n";
        $sampleSize = min(10, $totalProducts);
        for ($i = 0; $i < $sampleSize; $i++) {
            echo "Product ID: {$simpleProducts[$i]['entity_id']} | SKU: {$simpleProducts[$i]['sku']}\n";
        }
        
        if ($totalProducts > 10) {
            echo "... and " . ($totalProducts - 10) . " more products\n";
        }
    }
    
    echo "\n==========================\n";
    echo "Bulk Update Complete!\n";
    echo "==========================\n";
    echo "Total Simple Products: {$totalProducts}\n";
    echo "Qty set to: {$newQty}\n";
    echo "Stock Status: In Stock\n";
    
    // Clear cache
    echo "\nClearing cache...\n";
    $cacheManager = $objectManager->get(\Magento\Framework\App\Cache\Manager::class);
    $cacheManager->clean(['config', 'full_page']);
    echo "Cache cleared!\n";
    
} catch (\Exception $e) {
    echo "Fatal Error: " . $e->getMessage() . "\n";
    echo $e->getTraceAsString() . "\n";
}