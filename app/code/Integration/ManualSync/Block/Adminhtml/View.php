<?php
namespace Integration\ManualSync\Block\Adminhtml;

class View extends \Magento\Backend\Block\Template
{
	/**
     * @param \Magento\Backend\Block\Widget\Context $context
     * @param array $data
     */
    public function __construct(
    	\Magento\Backend\Block\Widget\Context $context,
        \Integration\ManualSync\Helper\Data $manualHelper,
		\Integration\ManualSync\Model\Watch $integrationManualSync,
        \Integration\Cron\Block\Adminhtml\Cron $integrationCron,
        \Integration\Cron\Model\Buffer $cronBuffer,
    	array $data = []
    ){
    	$this->integrationManualSync = $integrationManualSync;
        $this->manualHelper = $manualHelper;
        $this->integrationCron = $integrationCron;
        $this->cronBuffer = $cronBuffer;
        parent::__construct($context, $data);
    }

    public function getImportDirUrl()
    {
        return $this->getBaseUrl() . 'integration' . "/";
    }

    public function getEntitySyncUrl($typeId,$storeId = null)
    {
        return $this->getImportDirUrl() . "manualsync/run.php?type=".$typeId."&store_id=".$storeId;
    }
    
    /**
     * Returns running entity in sync process
     *
     * @param int $typeId
     * @return data|bool
     */
    public function checkManualRunningBatch($typeId,$storeId = 1)
    {
        if($typeId && !empty($typeId)){
            $websiteId = $this->manualHelper->getWebsiteIdByStoreId($storeId);
    	    $integrationManualSyncWatch = $this->integrationManualSync->getCollection()
    								->addFieldToFilter('type', array('eq' => $typeId))
    								->addFieldToFilter('status', array('eq' => \Integration\ManualSync\Model\Status::SYNC_RUNNING_STATUS))
                                    ->addFieldToFilter('website_id',['eq' => $websiteId])
                                    ->setOrder('api_entity_id', 'DESC')
    								->getFirstItem();

        	if($integrationManualSyncWatch && $integrationManualSyncWatch->getId()){
        		return $integrationManualSyncWatch;
        	}
        }

    	return false;
    }

    /**
     * Returns running batch history of cron buffer sync process
     *
     * @return bool
     */
    public function checkCronRunningBatch($typeId,$storeId = 1)
    {
        if($typeId && !empty($typeId)){
            $websiteId = $this->manualHelper->getWebsiteIdByStoreId($storeId);
            $runningBatch = $this->cronBuffer->getCollection()
                        ->addFieldToFilter('status',['eq' => \Integration\ManualSync\Model\Status::SYNC_RUNNING_STATUS])
                        ->addFieldToFilter('type',['eq' => $typeId])
                        ->addFieldToFilter('website_id',['eq' => $websiteId])
                        ->addFieldToFilter('new_flag',['eq' => 1])
                        ->getFirstItem();

            if($runningBatch && $runningBatch->getId()){
                return $runningBatch;
            }
        }

        return false;
    }

    /**
     * Returns sync history of sync process
     *
     * @param int $typeId
     * @return bool
     */
    public function hasHistoryForTypeId($typeId)
    {
        $integrationManualSyncWatchHistory = $this->integrationManualSync->getCollection()
                                    ->addFieldToFilter('type', array('eq' => $typeId))
                                    ->setOrder('api_entity_id', 'DESC');

        if($integrationManualSyncWatchHistory && $integrationManualSyncWatchHistory->getSize()){
            return true;
        }

        return false;
    }

    /**
     * Returns history url
     *
     * @param int $typeId
     * @param int $storeId
     * @return string
     */
    public function getHistoryUrl($typeId, $storeId = null)
    {
        return $this->getUrl('manualsync/history', array('type' => $typeId, 'store_id' => $storeId));
    }

    /**
     * Returns log url
     *
     * @param int $typeId
     * @param int $storeId
     * @return string
     */
    public function getLogUrl($typeId, $storeId = null, $date = null,$process = 'dump')
    {
        return $this->getUrl('manualsync/logs', array('type' => $typeId, 'store_id' => $storeId, 'process' => $process, 'date' => $date));
    }

    /**
     * Returns entity syc url
     *
     * @param int $typeId
     * @param int $storeId
     * @return string
     */
    /*public function getEntitySyncUrl($typeId,$storeId = null)
    {
        return $this->getUrl('manualsync/run/index', array('type' => $typeId, 'store_id' => $storeId));
    }*/

    /**
     * Returns customer pricing entity syc url Price Type 1 = Customer Price Class, 2 = Customer
     * Default price type is Customer price class
     *
     * @param int $typeId
     * @param int $storeId
     * @param int $priceType
     * @return string
     */
    public function getCustomerPricingSyncUrl($typeId,$storeId = null,$priceType = 1)
    {
        return $this->getUrl('manualsync/run/index', array('type' => $typeId, 'store_id' => $storeId, 'price_type' => $priceType));
    }
    /*public function getCustomerPricingSyncUrl($typeId,$storeId = null,$priceType = 1)
    {
        return $this->getImportDirUrl() . "manualsync/run.php?type=".$typeId."&store_id=".$storeId."&price_type=".$priceType;
        //$this->getUrl('manualsync/run/index', array('type' => $typeId, 'store_id' => $storeId, 'price_type' => $priceType));
    }*/

    /**
     * Returns entity syc url
     *
     * @param int $typeId
     * @param int $storeId
     * @return string
     */
    public function getDormantUrl($typeId,$storeId = null)
    {
        return $this->getUrl('manualsync/dormant/process', array('type' => $typeId, 'store_id' => $storeId));
    }

    /**
     * Returns customer code form url for process the batch for individual customer.
     *
     * @param int $typeId
     * @param int $storeId
     * @return string
     */
    public function getCustomerPricingBatchSyncUrl($typeId,$storeId = null)
    {
        return $this->getUrl('pricing/index/index', array('type' => $typeId, 'store_id' => $storeId));
    }
}
