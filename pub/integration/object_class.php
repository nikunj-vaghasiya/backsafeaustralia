<?php
//EXIT('EXIT');
#error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('max_execution_time', -1);
ini_set('default_socket_timeout', 360);
ini_set('allow_url_fopen', true);
ini_set('memory_limit', '-1');
set_time_limit(-1);
use Magento\Framework\App\Bootstrap;
define('BASE_DIR','/home/customer/www/backsafeaustralia.com.au/public_html');
define('CURL_SSL_VERIFYPEER','false');
define('CURL_SSL_VERIFYHOST','false');
define('DATE_FORMAT','Ymd');

require(BASE_DIR.'/app/bootstrap.php');

$bootstrap = Magento\Framework\App\Bootstrap::create(BP, $_SERVER);
$GLOBALS['objectManager'] = $bootstrap->getObjectManager();
$storeManager = $GLOBALS['objectManager']->get('\Magento\Store\Model\StoreManagerInterface');
$state = $GLOBALS['objectManager']->get('\Magento\Framework\App\State');
$state->setAreaCode(\Magento\Framework\App\Area::AREA_GLOBAL);
$store = $storeManager->getStore();

$GLOBALS['scopeConfig'] = $GLOBALS['objectManager']->get('\Magento\Framework\App\Config\ScopeConfigInterface');
$GLOBALS['statusModel'] = $GLOBALS['objectManager']->get('\Integration\ManualSync\Model\Status');
$GLOBALS['coreResource'] = $GLOBALS['objectManager']->get('Magento\Framework\App\ResourceConnection');
$GLOBALS['manualSyncHelper'] = $GLOBALS['objectManager']->get('\Integration\ManualSync\Helper\Data');
$GLOBALS['manualSyncWatch'] = $GLOBALS['objectManager']->get('\Integration\ManualSync\Model\Watch');
$GLOBALS['digitalConfigHelperData'] = $GLOBALS['objectManager']->get('\Digital\Config\Helper\Data');

?>
