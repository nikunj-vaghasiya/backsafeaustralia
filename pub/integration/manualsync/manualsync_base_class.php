<?php
ini_set('display_errors',1);
const ENTITY_NAME = 'manualsync';
const RUNNING_STATUS = 'running';
const PENDING_STATUS = 'pending';
const SUCCESS_STATUS = 'success';
const ERROR_STATUS = 'error';
const WIISE_MANUALSYNC_DATE_FORMAT = 'Y-m-d H:i:s';

const PRODUCT_BATCH_SIZE = 10;

$objectClassDir = getObjectClassDirPath();

include($objectClassDir . 'object_class.php');

/* $GLOBALS['customerDump'] = $GLOBALS['objectManager']->get('\Integration\Customer\Model\Dump\Process');
$GLOBALS['customerBatchProcess'] = $GLOBALS['objectManager']->get('\Integration\Customer\Model\Batch\Process');
$GLOBALS['customerImport'] = $GLOBALS['objectManager']->get('\Integration\Customer\Model\Import\Process');
$GLOBALS['customerParentBatchProcess'] = $GLOBALS['objectManager']->get('\Integration\Customer\Model\Batch\Parent\Process');
$GLOBALS['customerParentImport'] = $GLOBALS['objectManager']->get('\Integration\Customer\Model\Import\Parent\Process'); */
$GLOBALS['productDump'] = $GLOBALS['objectManager']->get('\Integration\Product\Model\Dump\Process');
$GLOBALS['productBatchProcess'] = $GLOBALS['objectManager']->get('\Integration\Product\Model\Batch\Process');
$GLOBALS['productImport'] = $GLOBALS['objectManager']->get('\Integration\Product\Model\Import\Process');
/* $GLOBALS['customerPricing'] = $GLOBALS['objectManager']->get('\Integration\Pricing\Model\Dump\Process');
$GLOBALS['pricingBatch'] = $GLOBALS['objectManager']->get('\Integration\Pricing\Model\Batch\Process');
$GLOBALS['productPricingImport'] = $GLOBALS['objectManager']->get('\Integration\Pricing\Model\Import\Process'); */
/* $GLOBALS['customerDynamicPricing'] = $GLOBALS['objectManager']->get('\Integration\Pricing\Model\Dump\Customer\Process');
$GLOBALS['customerPricingBatch'] = $GLOBALS['objectManager']->get('\Integration\Pricing\Model\Batch\Customer\Process');
$GLOBALS['customerPricingImport'] = $GLOBALS['objectManager']->get('\Integration\Pricing\Model\Import\Customer\Process'); */

function getObjectClassDirPath()
{
	$currentDir =  __DIR__;
	return str_replace(ENTITY_NAME, '', $currentDir);
}
?>
