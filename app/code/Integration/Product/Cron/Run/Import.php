<?php

namespace Integration\Product\Cron\Run;

class Import {

	protected $intProductHelper;

	protected $productImport;

	protected $paginationLog;
	
	protected $manualSyncHelper;

	public function __construct(
		\Integration\Product\Helper\Data $intProductHelper,
		\Integration\Product\Model\Import\Process $productImport,
		\Integration\Base\Model\PaginationLog $paginationLog,
		\Integration\ManualSync\Helper\Data $manualSyncHelper
	) {
		$this->intProductHelper = $intProductHelper;
		$this->productImport = $productImport;
		$this->paginationLog = $paginationLog;
		$this->manualSyncHelper = $manualSyncHelper;
	}

	public function execute($schedule = null) {
		if (!$this->intProductHelper->isProductApiEnabled()) {
			return $this;
		}

		try {
			$this->productImport
				->setWebsiteId(\Integration\ManualSync\Helper\Data::AU_WEBSITE_ID)
				->setLogTypeId(\Integration\ManualSync\Helper\Data::PRODUCT_IMPORT_TYPE_ID)
				->setProcessTypeId(\Integration\ManualSync\Helper\Data::PRODUCT_DUMP_TYPE_ID)
				->runProcess($schedule);

			/* To Do final messaging */
		} catch (\Exception $e) {
			/* critical log */
			$this->intProductHelper->createLogFiles('Error => ' . $e->getMessage(), 'CRIT', $this->intProductHelper::PRODUCT_IMPORT_LOG, 'error.log');
			/* critical log */
		}

		return $this;
	}
}
