<?php

namespace Integration\Product\Model\Batch;

class Process
{
	protected $_websiteId = 1; /* Main Website */

	protected $_storeId = 1; /* Default Store View*/

	protected $_mode = 'auto';

	protected $_batchSize = 300;

	protected $_typeId = null;

	protected $_processTypeId = null;

	protected $apiProduct;
	protected $manualSyncWatch;
	protected $intProductHelper;
	protected $manualSyncHelper;
	protected $cronBufferFactory;

	public function __construct(
		\Integration\Product\Model\Product $apiProduct,
		\Integration\Product\Helper\Data $intProductHelper,
		\Integration\ManualSync\Model\Watch $manualSyncWatch,
		\Integration\ManualSync\Helper\Data $manualSyncHelper,
		\Integration\Cron\Model\BufferFactory $cronBufferFactory
	){
		$this->apiProduct = $apiProduct;
		$this->manualSyncWatch = $manualSyncWatch;
		$this->intProductHelper = $intProductHelper;
		$this->manualSyncHelper = $manualSyncHelper;
		$this->cronBufferFactory = $cronBufferFactory;
	}

	public function setWebsiteId(int $websiteid)
	{
		$this->_websiteId = $websiteid;

		return $this;
	}

	public function getWebsiteId()
	{
		return $this->_websiteId;
	}

	public function setStoreId(int $storeid)
	{
		$this->_storeId = $storeid;

		return $this;
	}

	public function getStoreId()
	{
		return $this->_storeId;
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

	public function setBatchSize(int $batchSize)
	{
		$this->_batchSize = $batchSize;

		return $this;
	}

	public function getBatchSize()
	{
		return $this->_batchSize;
	}

	public function setLogTypeId(int $typeId)
	{
		$this->_typeId = $typeId;

		return $this;
	}

	public function getLogTypeId()
	{
		if(is_null($this->_typeId)) {
			$this->setLogTypeId(\Integration\ManualSync\Helper\Data::PRODUCT_IMPORT_BATCH_TYPE_ID);
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
		if(is_null($this->_processTypeId)) {
			$this->setProcessTypeId(\Integration\ManualSync\Helper\Data::PRODUCT_DUMP_TYPE_ID);
		}

		return $this->_processTypeId;
	}

	public function runProcess()
	{
		try{
			$message = "No records found";
			$apiProductsCollection = $this->_getDumpCollection();
			if($apiProductsCollection && $apiProductsCollection->getSize()) {
				$totalBatch = $remaining = 0;
                $resultCount = $apiProductsCollection->getSize();
                if($resultCount > $this->getBatchSize()){
                    $totalBatch = (int)($resultCount/$this->getBatchSize());
                    $remaining = $resultCount - ($totalBatch * $this->getBatchSize());
                } else {
                    $totalBatch = 0;
                    $remaining = $resultCount;
                }

                /* Manual Sync Tracking */
                if($this->getMode() == \Integration\ManualSync\Helper\Data::MANUAL_MODE){
                	$maualSyncCronWatch = $this->manualSyncWatch->getLastRunningRecord(
                		$this->getLogTypeId(),
                		\Integration\ManualSync\Model\Status::SYNC_RUNNING_STATUS,
                		$this->getWebsiteId()
                	);

			    	if($maualSyncCronWatch && $maualSyncCronWatch->getId()){
			    		$this->manualSyncWatch->setId($maualSyncCronWatch->getId())->setTotalNumber($resultCount)->save();
			    	}
                }
				/* Manual Sync Tracking */

                $startFrom = 0;
                $endTo = ($this->getBatchSize() - 1);
                /*$recordIds = [];*/
                $batchIds = [];
                for($i = 1;$i <= $totalBatch; $i++){
                    if($i != 1){
                        $startFrom = $endTo + 1;
                        $endTo = ($this->getBatchSize() * $i) - 1;
                    }

                    /*$recordIds = (count($recordIds)) ? implode(",", $recordIds) : '';*/
                    $cronBuffer = $this->cronBufferFactory->create();
                    $cronBuffer->setData([
                    	'website_id' => $this->getWebsiteId(),
                    	'mode' => $this->getMode(),
                    	'type' => $this->getProcessTypeId(),
                    	'total_number' => $this->getBatchSize(),
                    	'record_ids' => null,
                    	'status' => \Integration\ManualSync\Model\Status::SYNC_PENDING_STATUS,
                    	'new_flag' => 1
                    ])->save();

                    $batchIds[] = $cronBuffer->getId();
                }

                if($remaining > 0){
                	$startFrom = $endTo + 1;
                    $endTo = ($startFrom + $remaining);

                    /*$recordIds = (count($recordIds)) ? implode(",", $recordIds) : '';*/

                    $cronBuffer = $this->cronBufferFactory->create();
                    $cronBuffer->setData([
                    	'website_id' => $this->getWebsiteId(),
                    	'mode' => $this->getMode(),
                    	'type' => $this->getProcessTypeId(),
                    	'total_number' => $remaining,
                    	'record_ids' => null,
                    	'status' => \Integration\ManualSync\Model\Status::SYNC_PENDING_STATUS,
                    	'new_flag' => 1
                    ])->save();

                    $batchIds[] = $cronBuffer->getId();
                }

                if($remaining > 0){
                    $totalBatch++;
                }

                if($totalBatch){
                    $finalBatch = $totalBatch + 2;
                    $time = ($finalBatch * 5);
                    echo $message = "Total batches created to update all products is : " . $totalBatch . " Batch(es).";
                    if(is_array($batchIds) && count($batchIds)) {
                    	$message = " Total batch: " . $totalBatch . " - Batch IDs: " . implode(',', $batchIds);
                    }
                    echo "<br/>";
                }
                echo 'Finished successfully.';
			} else{
                echo $message;
            }
            $this->intProductHelper->createLogFiles($message, 'INFO', $this->intProductHelper::PRODUCT_BATCH_LOG, 'success.log',$this->getWebsiteId());
            /*return $this;*/
		} catch(\Exception $e){
			/* TO DO */
			/* Manual Sync Tracking */
			$this->intProductHelper->createLogFiles($e->getMessage(), 'INFO', $this->intProductHelper::PRODUCT_BATCH_LOG, 'error.log',$this->getWebsiteId());

			if($this->getMode() == \Integration\ManualSync\Helper\Data::MANUAL_MODE){
				$maualSyncCronWatch = $this->manualSyncWatch->getLastRunningRecord(
											$this->getLogTypeId(),
											\Integration\ManualSync\Model\Status::SYNC_RUNNING_STATUS,
											$this->getWebsiteId()
										);

		    	if($maualSyncCronWatch && $maualSyncCronWatch->getId()){
		    		$this->manualSyncWatch->setId($maualSyncCronWatch->getId())->setStatus('error')->setErrorMessage($e->getMessage())->save();
		    	}
		    }
			/* Manual Sync Tracking */
		}
	}

	/**
	* Returns new dump product collection
	*
	* @return  \Integration\Product\Model\ResourceModel\Product\Collection $collection|false
	*/
	protected function _getDumpCollection()
	{
		$collection = $this->apiProduct->getCollection()
								->addFieldToFilter('new_flag',['eq' => \Integration\Product\Model\Import\Process::NEW_FLAG_YES])
								->addFieldToFilter('website_id', $this->getWebsiteId())
								->setCurPage(1);
		return $collection;
	}
}
?>
