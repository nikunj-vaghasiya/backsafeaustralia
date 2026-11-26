<?php

namespace Integration\Product\Cron\Run;

class Batch
{
	protected $intProductHelper;

	protected $batchProcess;

	protected $paginationLog;
	
	protected $manualSyncHelper;
	
	public function __construct(
		\Integration\Product\Helper\Data $intProductHelper,
		\Integration\Product\Model\Batch\Process $batchProcess,
		\Integration\Base\Model\PaginationLog $paginationLog,
		\Integration\ManualSync\Helper\Data $manualSyncHelper
	){
		$this->intProductHelper = $intProductHelper;
		$this->batchProcess = $batchProcess;
		$this->paginationLog = $paginationLog;
		$this->manualSyncHelper = $manualSyncHelper;
	}

	public function execute($schedule = null)
    {	
    	if(!$this->intProductHelper->isProductApiEnabled()){
    		return $this;
    	}

    	try{
    		$this->batchProcess
    			->setWebsiteId(\Integration\ManualSync\Helper\Data::AU_WEBSITE_ID)
                ->setLogTypeId(\Integration\ManualSync\Helper\Data::PRODUCT_IMPORT_BATCH_TYPE_ID)
                ->setProcessTypeId(\Integration\ManualSync\Helper\Data::PRODUCT_DUMP_TYPE_ID)
    			->runProcess();

			/* To Do final messaging */
    	} catch(\Exception $e){
    		/* critical log */
			$this->intProductHelper->createLogFiles('Error => '. $e->getMessage(), 'CRIT', $this->intProductHelper::PRODUCT_BATCH_LOG, 'error.log');
			/* critical log */
		}

		return $this;
    }  

}
