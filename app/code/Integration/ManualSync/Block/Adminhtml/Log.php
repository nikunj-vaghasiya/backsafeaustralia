<?php
namespace Integration\ManualSync\Block\Adminhtml;

use Magento\Framework\App\Filesystem\DirectoryList;

class Log extends \Magento\Backend\Block\Template
{
    /**
     * Root directory instance
     *
     * @var \Magento\Framework\Filesystem\Directory\ReadInterface
     */
    protected $directory;

    /**
     * Var directory instance
     *
     * @var \Magento\Framework\Filesystem\Directory\ReadInterface
     */
    protected $varDirectory;

    /**
     * Integration Import logs directory instance
     *
     * @var \Magento\Framework\Filesystem\Directory\ReadInterface
     */
    private $integrationLogsDirectory;

    /**
     * @param \Magento\Backend\Block\Widget\Context $context
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Widget\Context $context,
        \Integration\ManualSync\Helper\Data $manualSyncHelper,
        array $data = []
    ) {
        $this->_filesystem = $context->getFilesystem();
        parent::__construct($context, $data);
        $this->manualSyncHelper = $manualSyncHelper;
    }

    protected function getVarDirectory()
    {
        if (null === $this->varDirectory) {
            $this->varDirectory = $this->_filesystem->getDirectoryRead(DirectoryList::VAR_DIR);
        }

        return $this->varDirectory;
    }

    public function getLogDescriptionByFileName($filename)
    {
        return $this->manualSyncHelper->getLogDescriptionByFileName($filename);
    }

    public function getEntityDirNameByType($type)
    {
        return $this->manualSyncHelper->getEntityDirNameByType($type);
    }

    public function getIntegrationLogsDir($entityDir = null, $dateDir = null, $storeId = null, $processType = null)
    {
        if (($entityDir == null || $entityDir == '')
            && ($dateDir == null || $dateDir == '')
            && ($storeId == null || $storeId == '')
            && ($processType == null || $processType == '')
        ) {
            return '';
        }

        $integrationLogsDir = \Integration\Base\Helper\AbstractHelper::LOG_DIR_NAME;
        $websiteId = $this->manualSyncHelper->getWebsiteIdByStoreId($storeId);
        if ($websiteId == \Integration\ManualSync\Helper\Data::NZ_WEBSITE_ID) {
            $integrationLogsDir = $integrationLogsDir . '/' . $websiteId;
        } elseif ($websiteId == \Integration\ManualSync\Helper\Data::AU_WEBSITE_ID) {
            $integrationLogsDir = $integrationLogsDir . '/' . $websiteId;
        }

        if (!$integrationLogsDir) {
            return '';
        }

        $dynamicRelativePath = $integrationLogsDir . '/' . $dateDir . '/' . $entityDir;
        $this->integrationLogsDirectory = $this->getVarDirectory()->getRelativePath($dynamicRelativePath);

        return $this->integrationLogsDirectory;
    }

    public function getLogFiles()
    {
        $logFiles = [];

        $entityDir = $this->getEntityDirNameByType($this->getRequest()->getParam('type', false));
        $dateDir = $this->getRequestedDate();
        $storeId = $this->getRequest()->getParam('store_id', '');
        $processType = $this->getRequest()->getParam('process', '');
        $logsDirectoryRelativePath = $this->getIntegrationLogsDir($entityDir, $dateDir, $storeId, $processType);

        if (!$logsDirectoryRelativePath || $logsDirectoryRelativePath == '') {
            return $logFiles;
        }

        if ($this->getVarDirectory()->isExist($logsDirectoryRelativePath)) {
            $logFiles = $this->getVarDirectory()->read($logsDirectoryRelativePath);
        }
        return $logFiles;
    }

    public function getDateSelectOption()
    {
        $option = array();
        $dateRange = $this->getDatesFromRange($this->manualSyncHelper->getPreviousDate(), $this->manualSyncHelper->getCurrentDate(), 'Ymd');

        $entityDir = $this->getEntityDirNameByType($this->getRequest()->getParam('type', false));
        $storeId = $this->getRequest()->getParam('store_id', '');
        $processType = $this->getRequest()->getParam('process', '');

        if (count($dateRange)) {
            foreach ($dateRange as $date) {
                $logsDirectoryRelativePath = $this->getIntegrationLogsDir($entityDir, $date, $storeId, $processType);
                if ($this->getVarDirectory()->isExist($logsDirectoryRelativePath)) {
                    $option[$date] = date('d M Y', strtotime($date));
                }
            }
        }

        return $option;
    }

    public function getRequestedDate()
    {
        return $this->getRequest()->getParam('date', $this->manualSyncHelper->getCurrentDate('Ymd'));
    }

    public function getDatesFromRange($startDate, $endDate, $format = 'Y-m-d')
    {
        $dateRange = [];

        $strToTimeStart = strtotime($startDate);
        $strtoTimeEnd = strtotime($endDate);

        /*Use for loop to store dates into array */
        /*86400 sec = 24 hrs = 60*60*24 = 1 day */
        for ($currentDate = $strToTimeStart; $currentDate <= $strtoTimeEnd; $currentDate += (86400)) {
            $store = date($format, $currentDate);
            $dateRange[] = $store;
        }

        if (is_array($dateRange) && count($dateRange) > 0) {
            arsort($dateRange);
        }
        // Return the array elements
        return $dateRange;
    }

    public function getLogUrl($typeId, $storeId = null, $date = null, $process = 'dump')
    {
        return $this->getUrl('manualsync/logs', array('type' => $typeId, 'store_id' => $storeId, 'process' => $process, 'date' => $date));
    }

    public function getDownloadUrl($typeId, $storeId = null, $date = null, $filename = null)
    {
        return $this->getUrl('manualsync/logs/download', array('type' => $typeId, 'store_id' => $storeId, 'date' => $date, 'filename' => $filename));
    }

    public function getBackUrl($storeId = 1)
    {
        return ($storeId == 1) ? $this->getUrl('manualsync/index/index/', array('store_id' => $storeId)) : $this->getUrl('manualsync/index/indexnz/', array('store_id' => $storeId));
    }
}
