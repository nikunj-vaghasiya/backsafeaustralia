<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$supplier = 'STURGO®,WERKS®,Rubbermaid,Nally,Clax,Branach,Stockmaster,Storemasta,Steprite,Boscotek,Backsafe Australia';


?>


<?php
use Magento\Framework\App\Bootstrap;

require __DIR__ . '/../app/bootstrap.php';

$bootstrap = Bootstrap::create(BP, $_SERVER);
$obj = $bootstrap->getObjectManager();
$state = $obj->get(\Magento\Framework\App\State::class);
$state->setAreaCode('adminhtml');

$eavSetupFactory = $obj->get(\Magento\Eav\Setup\EavSetupFactory::class);
$eavConfig = $obj->get(\Magento\Eav\Model\Config::class);

$eavSetup = $eavSetupFactory->create(['setup' => $obj->get('Magento\Framework\Setup\ModuleDataSetupInterface')]);

$attributeCode = 'product_brand'; // your product attribute code
$optionsToAdd = explode(",",$supplier); // add options here

try {
    $attribute = $eavConfig->getAttribute(\Magento\Catalog\Model\Product::ENTITY, $attributeCode);

    if (!$attribute || !$attribute->getId()) {
        throw new \Exception("Attribute '$attributeCode' not found.");
    }

    $optionManagement = $obj->get(\Magento\Eav\Api\AttributeOptionManagementInterface::class);
    $optionFactory = $obj->get(\Magento\Eav\Api\Data\AttributeOptionInterfaceFactory::class);
    $optionLabelFactory = $obj->get(\Magento\Eav\Api\Data\AttributeOptionLabelInterfaceFactory::class);

    foreach ($optionsToAdd as $label) {
        $option = $optionFactory->create();
        $optionLabel = $optionLabelFactory->create();
        $optionLabel->setStoreId(0);
        $optionLabel->setLabel(ucfirst($label));

        $option->setLabel($label);
        $option->setStoreLabels([$optionLabel]);
        $option->setSortOrder(0);
        $option->setIsDefault(false);

        $optionManagement->add(
            \Magento\Catalog\Model\Product::ENTITY,
            $attributeCode,
            $option
        );

        echo "Added option: $label" ."\n";
    }

    echo "All done!" . PHP_EOL;

} catch (\Exception $e) {
    echo 'Error: ' . $e->getMessage() . PHP_EOL;
}
