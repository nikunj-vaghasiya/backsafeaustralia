<?php

namespace Integration\Product\Cron\Run;

class Dump
{
	protected $intProductHelper;

	protected $dumpProcess;

	protected $paginationLog;
	
	protected $manualSyncHelper;

	public function __construct(
		\Integration\Product\Helper\Data $intProductHelper,
		\Integration\Product\Model\Dump\Process $dumpProcess,
		\Integration\Base\Model\PaginationLog $paginationLog,
		\Integration\ManualSync\Helper\Data $manualSyncHelper
	){
		$this->intProductHelper = $intProductHelper;
		$this->dumpProcess = $dumpProcess;
		$this->paginationLog = $paginationLog;
		$this->manualSyncHelper = $manualSyncHelper;
	}

	public function execute($schedule = null)
    {	
    	if(!$this->intProductHelper->isProductApiEnabled()){
    		return $this;
    	}

    	try{
    		/* TO DO - In pagination scenario there is separate cron need to manage to reset the status of automation */
    		if(!$this->intProductHelper->isPaginationEnabled()) {
    			$this->paginationLog->updatePaginationLog(
					\Integration\ManualSync\Helper\Data::PRODUCT_DUMP_TYPE_ID,
					['current_auto_counter' => 0,'auto_status' => 1,'last_auto_run' => $this->manualSyncHelper->getCurrentDatetime()]
				);
    		}

    		$this->dumpProcess
    			->setWebsiteId(\Integration\ManualSync\Helper\Data::AU_WEBSITE_ID)
                ->setLogTypeId(\Integration\ManualSync\Helper\Data::PRODUCT_DUMP_TYPE_ID)
    			->runProcess();

			/* To Do final messaging */
    	} catch(\Exception $e){
    		/* critical log */
			$this->intProductHelper->createLogFiles('Error => '. $e->getMessage(), 'CRIT', $this->intProductHelper::PRODUCT_DUMP_LOG, 'error.log');
			/* critical log */
		}

		return $this;
    }  

}
