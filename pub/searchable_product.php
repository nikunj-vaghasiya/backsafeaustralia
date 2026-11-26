<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('max_execution_time', 0);
ini_set('memory_limit', '-1');
set_time_limit(0);
use Magento\Framework\App\Bootstrap;
include('../app/bootstrap.php');
$bootstrap = Bootstrap::create(BP, $_SERVER);
$objectManager = $bootstrap->getObjectManager();
$url = \Magento\Framework\App\ObjectManager::getInstance();
$storeManager = $url->get('\Magento\Store\Model\StoreManagerInterface');

$state = $objectManager->get('\Magento\Framework\App\State');
$state->setAreaCode(\Magento\Framework\App\Area::AREA_GLOBAL);

$GLOBALS['productModel'] = $GLOBALS['objectManager']->get('Magento\Catalog\Model\Product');

try{
    $_productColl = $GLOBALS['productModel']->getCollection()->addAttributeToSelect('*')
                                        //->addAttributeToFilter('entity_id',array('gt'=>5533))
                                        //->addAttributeToFilter('entity_id',9)
                                        ->addAttributeToFilter('type_id', array('eq' => 'grouped'));
    
    foreach($_productColl as $_product){
		
        $associatedProducts = $_product->getTypeInstance(true)->getAssociatedProducts($_product);
		
        if($associatedProducts && count($associatedProducts)){		
            $tp_search_sku = '';
            foreach($associatedProducts as $child){
                    $tp_search_sku  .= $child->getSku(). ", ";
                    
                
            }
            $tp_search_sku = rtrim($tp_search_sku, ', ');
           // echo '===>'.$tp_search_sku;
            $_product->setData('searchable_codes',$tp_search_sku);
            
            $_product->save(); 
            echo "==>".$_product->getId()." => Success";
			echo '<br/>';
        }
        $_product->unsetData();
    }
} catch (Exception $e){
    echo $e->getMessage(); //exit('Exit');
}
