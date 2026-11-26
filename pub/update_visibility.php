<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

use Magento\Framework\App\Bootstrap;
require_once __DIR__ . '/../app/bootstrap.php';

$bootstrap = Bootstrap::create(BP, $_SERVER);
$objectManager = $bootstrap->getObjectManager();

$state = $objectManager->get(\Magento\Framework\App\State::class);
$state->setAreaCode(\Magento\Framework\App\Area::AREA_ADMINHTML);

$categoryId = 102;

// Get category
$categoryRepository = $objectManager->get(\Magento\Catalog\Api\CategoryRepositoryInterface::class);
$category = $categoryRepository->get($categoryId);

// Get product collection for this category
$productCollection = $objectManager->create(\Magento\Catalog\Model\ResourceModel\Product\Collection::class);
$productCollection->addCategoriesFilter(['eq' => $categoryId]);

// Get product action to update attributes
$productAction = $objectManager->get(\Magento\Catalog\Model\Product\Action::class);

$productIds = $productCollection->getAllIds();
$storeId = 0; // 0 for all store views, or specify store ID

echo "Found " . count($productIds) . " products in category ID " . $categoryId . "\n";

if (count($productIds) > 0) {
    // Set visibility to "Not Visible Individually" (value = 1)
    $productAction->updateAttributes(
        $productIds,
        ['visibility' => \Magento\Catalog\Model\Product\Visibility::VISIBILITY_NOT_VISIBLE],
        $storeId
    );
    
    echo "Successfully updated visibility for " . count($productIds) . " products.\n";
} else {
    echo "No products found in this category.\n";
}

// Reindex
$indexerFactory = $objectManager->get(\Magento\Indexer\Model\IndexerFactory::class);
$indexer = $indexerFactory->create();
$indexer->load('catalog_product_attribute');
$indexer->reindex();

echo "Reindexing completed.\n";