<?php
include('manualsync_base_class.php');

$typeId = (int)isset($_GET['type'])?$_GET['type']:'';
$storeId = isset($_GET['store_id'])?$_GET['store_id']:'';
//$priceType = isset($_GET['price_type'])?$_GET['price_type']:'';

if($typeId == '' || $storeId == ''){
    return "Data is missing, please contact to administrator";
}
?>
<html lang="en">
<head >
<link rel="shortcut icon" type="image/jpg" href="<?php echo $GLOBALS['digitalConfigHelperData']->getFaviconPath(); ?>"/>
<link rel="icon" type="image/x-icon" href="<?php echo $GLOBALS['digitalConfigHelperData']->getFaviconPath(); ?>" />
</head>
<body>

<?php

runProcess($typeId,$storeId);

function runProcess($typeId,$storeId = null)
{
    //echo $priceType = isset($_GET['price_type'])?$_GET['price_type']:'';
    //
    $websiteId = $GLOBALS['manualSyncHelper']->getWebsiteIdByStoreId($storeId);
    
    $mode = $GLOBALS['manualSyncHelper']::MANUAL_MODE;

    $output = '';
    
    if(isAlreadyRunningSync($typeId,$websiteId)){
        return "One of sync already running. Please wait..";
    }

    $GLOBALS['manualSyncWatch']->setType($typeId)
        ->setStartAt($GLOBALS['manualSyncHelper']->getCurrentDatetime())
        ->setStatus($GLOBALS['statusModel']::SYNC_RUNNING_STATUS)
        ->setWebsiteId($websiteId)
        ->save();

    switch ($typeId) {
       /*  case $GLOBALS['manualSyncHelper']::CUSTOMER_DUMP_TYPE_ID:
            echo "Start Process.<br />";
            $output = $GLOBALS['customerDump']
                    ->setWebsiteId($websiteId)
                    ->setMode($mode)
                    ->runProcess();
            echo "<br />";
            echo "End Process.<br />";
            break;

        case $GLOBALS['manualSyncHelper']::CUSTOMER_IMPORT_BATCH_TYPE_ID:
            echo "Start Process.<br />";
            $output = $GLOBALS['customerBatchProcess']
                    ->setWebsiteId($websiteId)
                    ->setMode($mode)
                    ->setLogTypeId($GLOBALS['manualSyncHelper']::CUSTOMER_IMPORT_BATCH_TYPE_ID)
                    ->setProcessTypeId($GLOBALS['manualSyncHelper']::CUSTOMER_DUMP_TYPE_ID)
                    ->runProcess();
            echo "<br />";
            echo "End Process.<br />";
            break;

        case $GLOBALS['manualSyncHelper']::CUSTOMER_IMPORT_TYPE_ID:
            echo "Start Process.<br />";
            $output = $GLOBALS['customerImport']
                    ->setWebsiteId($websiteId)
                    ->setMode($mode)
                    ->setLogTypeId($GLOBALS['manualSyncHelper']::CUSTOMER_IMPORT_TYPE_ID)
                    ->setProcessTypeId($GLOBALS['manualSyncHelper']::CUSTOMER_DUMP_TYPE_ID)
                    ->runProcess();
            echo "<br />";
            echo "End Process.<br />";
            break;
        case $GLOBALS['manualSyncHelper']::CUSTOMER_PARENT_ASSIGNMENT_IMPORT_BATCH_TYPE_ID:
            echo "Start Process.<br />";
            $output = $GLOBALS['customerParentBatchProcess']
                        ->setWebsiteId($websiteId)
                        ->setMode($mode)
                        ->setLogTypeId($GLOBALS['manualSyncHelper']::CUSTOMER_PARENT_ASSIGNMENT_IMPORT_BATCH_TYPE_ID)
                        ->setProcessTypeId($GLOBALS['manualSyncHelper']::CUSTOMER_PARENT_ASSIGNMENT_DUMP_TYPE_ID)
                        ->runProcess();
            break;
        case $GLOBALS['manualSyncHelper']::CUSTOMER_PARENT_ASSIGNMENT_IMPORT_TYPE_ID:
            echo "Start Process.<br />";
            $output = $GLOBALS['customerParentImport']
                    ->setWebsiteId($websiteId)
                    ->setMode($mode)
                    ->setLogTypeId($GLOBALS['manualSyncHelper']::CUSTOMER_PARENT_ASSIGNMENT_IMPORT_TYPE_ID)
                    ->setProcessTypeId($GLOBALS['manualSyncHelper']::CUSTOMER_PARENT_ASSIGNMENT_DUMP_TYPE_ID)
                    ->runProcess();
            echo "<br />";
            echo "End Process.<br />";
            break; */
        case $GLOBALS['manualSyncHelper']::PRODUCT_DUMP_TYPE_ID:
            echo "Start Process.1<br />";
            $output = $GLOBALS['productDump']
                    ->setWebsiteId($websiteId)
                    ->setMode($mode)
                    ->setLogTypeId($GLOBALS['manualSyncHelper']::PRODUCT_DUMP_TYPE_ID)
                    ->runProcess();
            echo "<br />";
            echo "End Process.<br />";
            break;
        case $GLOBALS['manualSyncHelper']::PRODUCT_IMPORT_BATCH_TYPE_ID:
            echo "Start Process. 2<br />";
            $output = $GLOBALS['productBatchProcess']
                    ->setWebsiteId($websiteId)
                    ->setMode($mode)
                    ->setLogTypeId($GLOBALS['manualSyncHelper']::PRODUCT_IMPORT_BATCH_TYPE_ID)
                    ->setProcessTypeId($GLOBALS['manualSyncHelper']::PRODUCT_DUMP_TYPE_ID)
                    ->runProcess();
            echo "<br />";
            echo "End Process.<br />";
            break;
        case $GLOBALS['manualSyncHelper']::PRODUCT_IMPORT_TYPE_ID:
            echo "Start Process. 3<br />";
            $output = $GLOBALS['productImport']
                    ->setWebsiteId($websiteId)
                    ->setMode($mode)
                    ->setLogTypeId($GLOBALS['manualSyncHelper']::PRODUCT_IMPORT_TYPE_ID)
                    ->setProcessTypeId($GLOBALS['manualSyncHelper']::PRODUCT_DUMP_TYPE_ID)
                    ->runProcess();
            echo "<br />";
            echo "End Process.<br />";
            break;
       /*  case $GLOBALS['manualSyncHelper']::CUSTOMER_PRICING_GUEST_DUMP_TYPE_ID:
            echo "Start Process.<br />";
            $output = $GLOBALS['customerPricing']
                    ->setWebsiteId($websiteId)
                    ->setMode($mode)
                    ->setProcessTypeId($GLOBALS['manualSyncHelper']::CUSTOMER_PRICING_GUEST_DUMP_TYPE_ID)
                    ->runProcess();
            echo "<br />";
            echo "End Process.<br />";
            break;
        case $GLOBALS['manualSyncHelper']::CUSTOMER_PRICING_BATCH_TYPE_ID:
            echo "Start Process.<br />";
            $output = $GLOBALS['pricingBatch']
                    ->setWebsiteId($websiteId)
                    ->setMode($mode)
                    ->setLogTypeId($GLOBALS['manualSyncHelper']::CUSTOMER_PRICING_BATCH_TYPE_ID)
                    ->setProcessTypeId($GLOBALS['manualSyncHelper']::CUSTOMER_PRICING_GUEST_DUMP_TYPE_ID)
                    ->runProcess();
            echo "<br />";
            echo "End Process.<br />";
            break;
        case $GLOBALS['manualSyncHelper']::CUSTOMER_PRICING_IMPORT_TYPE_ID:
            echo "Start Process.<br />";
            $output = $GLOBALS['productPricingImport']
                    ->setWebsiteId($websiteId)
                    ->setMode($mode)
                    ->setLogTypeId($GLOBALS['manualSyncHelper']::CUSTOMER_PRICING_IMPORT_TYPE_ID)
                    ->setProcessTypeId($GLOBALS['manualSyncHelper']::CUSTOMER_PRICING_GUEST_DUMP_TYPE_ID)
                    ->runProcess();
            echo "<br />";
            echo "End Process.<br />";
            break;
        case $GLOBALS['manualSyncHelper']::CUSTOMER_PRICING_DUMP_TYPE_ID:
            echo "Start Process.<br />";
            $output = $GLOBALS['customerDynamicPricing']
                    ->setWebsiteId($websiteId)
                    ->setMode($mode)
                    ->setPriceType($priceType)
                    ->setProcessTypeId($GLOBALS['manualSyncHelper']::CUSTOMER_PRICING_DUMP_TYPE_ID)
                    ->runProcess();
            echo "<br />";
            echo "End Process.<br />";
            break;
        case $GLOBALS['manualSyncHelper']::CUSTOMER_DYNAMIC_PRICING_BATCH_TYPE_ID:
            echo "Start Process.<br />";
            $output = $GLOBALS['customerPricingBatch']
                    ->setWebsiteId($websiteId)
                    ->setMode($mode)
                    ->setLogTypeId($GLOBALS['manualSyncHelper']::CUSTOMER_DYNAMIC_PRICING_BATCH_TYPE_ID)
                    ->setProcessTypeId($GLOBALS['manualSyncHelper']::CUSTOMER_PRICING_DUMP_TYPE_ID)
                    ->runProcess();
            echo "<br />";
            echo "End Process.<br />";
            break;
        case $GLOBALS['manualSyncHelper']::CUSTOMER_DYNAMIC_PRICING_IMPORT_TYPE_ID:
            echo "Start Process.<br />";
            $output = $GLOBALS['customerPricingImport']
                    ->setWebsiteId($websiteId)
                    ->setMode($mode)
                    ->setLogTypeId($GLOBALS['manualSyncHelper']::CUSTOMER_DYNAMIC_PRICING_IMPORT_TYPE_ID)
                    ->setProcessTypeId($GLOBALS['manualSyncHelper']::CUSTOMER_PRICING_DUMP_TYPE_ID)
                    ->runProcess();
            echo "<br />";
            echo "End Process.<br />";
            break; */
        default:
           echo 'We can not find what you want, sorry';
           break;
    }

    if(isAlreadyRunningSync($typeId,$websiteId)){
        $GLOBALS['manualSyncWatch']->setType($typeId)
            ->setEndAt($GLOBALS['manualSyncHelper']->getCurrentDatetime())
            ->setStatus($GLOBALS['statusModel']::SYNC_SUCCESS_STATUS)
            ->setWebsiteId($websiteId)
            ->save();
    }

    return $output;
}

function isAlreadyRunningSync($typeId,$websiteId = null)
{
    $lastRunningRecord = $GLOBALS['manualSyncWatch']->getLastRunningRecord($typeId,$GLOBALS['statusModel']::SYNC_RUNNING_STATUS,$websiteId);;

    if($lastRunningRecord && $lastRunningRecord->getId()){
        return true;
    }

    return false;
}
?>

</body>
</html>

<style> table.products-table{border-collapse: collapse;border-spacing: 0;} .products-table th{background:#eee;} .products-table th, .products-table td{border:1px solid #CCC; padding:5px; text-align:center} .products-table td input{width: 100%; max-width: 370px; display: block; padding: 5px;}.products-table td.description , .products-table td.name{ min-width:600px;max-width:680px; text-align:left}.products-table td.name{ min-width:300px;max-width:300px; }.products-table td.description{text-align:left}
</style>
<?php
die;
?>