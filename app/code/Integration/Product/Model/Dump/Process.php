<?php

namespace Integration\Product\Model\Dump;

class Process
{
	protected $_websiteId = 1; /* Main Website */

	protected $_storeId = 1; /* Default Store View*/

	protected $_mode = 'auto';

	protected $_typeId = null;

	protected $intProductHelper;
	protected $fieldMapping;
	protected $apiProduct;
	protected $httpClient;
	protected $paginationLog;
	protected $manualSyncWatch;
	protected $manualSyncHelper;

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

	public function setLogTypeId(int $typeId)
	{
		$this->_typeId = $typeId;

		return $this;
	}

	public function getLogTypeId()
	{
		if (is_null($this->_typeId)) {
			$this->setLogTypeId(\Integration\ManualSync\Helper\Data::PRODUCT_DUMP_TYPE_ID);
		}

		return $this->_typeId;
	}

	public function __construct(
		\Integration\Product\Helper\Data $intProductHelper,
		\Integration\Product\Model\Product\FieldMapping $fieldMapping,
		\Integration\Product\Model\Product $apiProduct,
		\Integration\Base\Model\HTTP\Client $client,
		\Integration\Base\Model\PaginationLog $paginationLog,
		\Integration\ManualSync\Model\Watch $manualSyncWatch,
		\Integration\ManualSync\Helper\Data $manualSyncHelper
	) {
		$this->intProductHelper = $intProductHelper;
		$this->fieldMapping = $fieldMapping;
		$this->apiProduct = $apiProduct;
		$this->httpClient = $client;
		$this->paginationLog = $paginationLog;
		$this->manualSyncWatch = $manualSyncWatch;
		$this->manualSyncHelper = $manualSyncHelper;
	}

	public function runProcess()
	{
		$paginationLog = $this->paginationLog->checkLogStatus($this->getLogTypeId(), $this->getMode());

		/* Modified parameters for manual sync start */
		if ($this->getMode() == \Integration\ManualSync\Helper\Data::MANUAL_MODE && ($paginationLog && !$paginationLog->getId())) {
			$this->paginationLog->updatePaginationLog(
				$this->getLogTypeId(),
				['current_manual_counter' => 0, 'manual_status' => 1, 'last_manual_run' => $this->manualSyncHelper->getCurrentDatetime()]
			);

			$paginationLog = $this->paginationLog->checkLogStatus($this->getLogTypeId(), $this->getMode());
		}
		/* Modified parameters for manual sync start */

		if (!$paginationLog || ($paginationLog->getId() && !$paginationLog->getId())) {
			echo "Sync not enabled or wrong trigger called";
			return $this;
		}

		try {
			$logCounter = $paginationLog->getLogCounter();
			$currentCounter = ($this->getMode() == \Integration\ManualSync\Helper\Data::MANUAL_MODE) ? $paginationLog->getCurrentManualCounter() : $paginationLog->getCurrentAutoCounter();
			$status = ($this->getMode() == \Integration\ManualSync\Helper\Data::MANUAL_MODE) ? $paginationLog->getManualStatus() : $paginationLog->getAutoStatus();

			$newCounter = $currentCounter + 1;
			$newStatus = ($logCounter == $newCounter) ? 0 : 1; /* 0 = Stop and 1 = New */

			$updateParams = ['current_auto_counter' => (int)$newCounter, 'auto_status' => $newStatus];
			if ($this->getMode() == \Integration\ManualSync\Helper\Data::MANUAL_MODE) {
				$updateParams = ['current_manual_counter' => (int)$newCounter, 'manual_status' => $newStatus];
			}

			$this->paginationLog->updatePaginationLog(
				$this->getLogTypeId(),
				$updateParams
			);

			/* TO DO - In pagination API we need to pass $newCounter as page in API request */
			$requestUrl = $this->intProductHelper->getProductAPIEndpointUrl();

			$response = $this->httpClient->apiGetRequest($requestUrl);
			$this->intProductHelper->createLogFiles('Request Url => ' . $requestUrl, 'INFO', $this->intProductHelper::PRODUCT_REQUEST_RESPONSE_LOG, 'request-response.log', $this->getWebsiteId());
			if (is_array($response) && $response['status'] === 200 && count($response['object']) >= 1) {
				/* response log */
				$this->intProductHelper->createLogFiles('Response => ' . json_encode($response), 'INFO', $this->intProductHelper::PRODUCT_REQUEST_RESPONSE_LOG, 'request-response.log', $this->getWebsiteId());
				/* response log */

				/* Manual Sync Tracking */
				if ($this->getMode() == \Integration\ManualSync\Helper\Data::MANUAL_MODE) {
					$maualSyncCronWatch = $this->manualSyncWatch->getLastRunningRecord(
						$this->getLogTypeId(),
						\Integration\ManualSync\Model\Status::SYNC_RUNNING_STATUS,
						$this->getWebsiteId()
					);

					if ($maualSyncCronWatch && $maualSyncCronWatch->getId()) {
						$this->manualSyncWatch->setId($maualSyncCronWatch->getId())->setTotalNumber($response['count'])->save();
					}

					/* If pagination not enabled then manual sync status disabled after run first page */
					if (!$this->intProductHelper->isPaginationEnabled()) {
						$this->paginationLog->updatePaginationLog(
							$this->getLogTypeId(),
							['manual_status' => 0]
						);
					}
					/* If pagination not enabled then manual sync status disabled after run first page */
				}
				/* Manual Sync Tracking */

				$productMapping = $this->fieldMapping->get();
				/* debug log */
				$this->intProductHelper->createLogFiles('=== NEW DUMP START ===', 'DEBUG', $this->intProductHelper::PRODUCT_DUMP_LOG, 'success.log', $this->getWebsiteId());
				/* debug log */
				foreach ($response['object'] as $productData) {
					//echo "<pre>";print_r($productData['properties']);echo "</pre>";
					$huspot_properties = $productData['properties'];
					$responseData = new \Integration\Base\Model\DataObject();
					$responseData->setData($huspot_properties);
					$data = [];
					if ($huspot_properties['hs_status'] == 'active') {
						if($huspot_properties['status'] == 'STOCK'){ 
							$is_in_stock = 1;
						}else{
							$is_in_stock = 0;
						}
						$data['name'] = $huspot_properties['name'];
						$data['sku'] = $huspot_properties['hs_sku'];
						$data['price'] = $huspot_properties['price'];
						$data['quantity'] = $huspot_properties['bc_inventory'];
                        $data['description'] = $huspot_properties['description'];
                        $data['supplier'] = $huspot_properties['supplier'];
						$data['stock_status'] = $is_in_stock;
						$data['weight'] = $huspot_properties['weight'];						
						$data['status'] = 1;
						$data['website_id'] = $this->getWebsiteId();
						$data['new_flag'] = 1;
						$product = $this->apiProduct->loadProductBySku($data['sku'], $this->getWebsiteId());
						$msg = "Product created successfully with sku : " . $data['sku'];

						if ($product && $product->getId()) {
							$data['api_entity_id'] = $product->getId();
							$msg = "Product updated successfully with sku : " . $product->getSku() . " - Row ID: " . $product->getId();
						}
						$this->apiProduct->setData($data);
						$this->apiProduct->save();

						echo $msg . "<br/>";


						$this->intProductHelper->createLogFiles($msg, 'DEBUG', $this->intProductHelper::PRODUCT_DUMP_LOG, 'success.log', $this->getWebsiteId());

						$this->apiProduct->setData([]);
					}
				}
				/* debug log */
				$this->intProductHelper->createLogFiles('=== NEW DUMP END ===', 'DEBUG', $this->intProductHelper::PRODUCT_DUMP_LOG, 'success.log', $this->getWebsiteId());
				/* debug log */

				if ($this->getMode() == \Integration\ManualSync\Helper\Data::MANUAL_MODE) {
					if ($currentCounter > 0 && $logCounter > 0) {
						echo "<br/>";
						echo "Page: " . $newCounter . " finished successfully.";
						echo "<br/>";
					}

					if ($this->intProductHelper->isPaginationEnabled() && $logCounter > 0 && $logCounter != $newCounter) {
						echo "<br/>";
						$nextPageUrl = ''; /* TO DO - Next page url */
						echo "To proceed the remaining, Please click - <a href='" . $nextPageUrl . "'>Next Page: " . ($newCounter + 1) . "</a>";
						echo "<br/>";
						echo "<br/>";
					}
				}
			} elseif (is_array($response) && $response['status'] === 200 && count($response['object']) <= 0) {
				$msg = 'No Records Found';

				echo $msg . "<br/>";
				$this->intProductHelper->createLogFiles('Response => ' . json_encode($response), 'INFO', $this->intProductHelper::PRODUCT_REQUEST_RESPONSE_LOG, 'request-response.log', $this->getWebsiteId());
				$this->intProductHelper->createLogFiles('=== NEW DUMP START ===', 'INFO', $this->intProductHelper::PRODUCT_DUMP_LOG, 'success.log', $this->getWebsiteId());
				$this->intProductHelper->createLogFiles($msg, 'INFO', $this->intProductHelper::PRODUCT_DUMP_LOG, 'success.log', $this->getWebsiteId());
				$this->intProductHelper->createLogFiles('=== NEW DUMP END ===', 'INFO', $this->intProductHelper::PRODUCT_DUMP_LOG, 'success.log', $this->getWebsiteId());

				/* Manual Sync Tracking */
				if ($this->getMode() == \Integration\ManualSync\Helper\Data::MANUAL_MODE) {
					$maualSyncCronWatch = $this->manualSyncWatch->getLastRunningRecord(
						$this->getLogTypeId(),
						\Integration\ManualSync\Model\Status::SYNC_RUNNING_STATUS,
						$this->getWebsiteId()
					);

					if ($maualSyncCronWatch && $maualSyncCronWatch->getId()) {
						$this->manualSyncWatch->setId($maualSyncCronWatch->getId())->setTotalNumber($response['count'])->save();
					}

					$this->paginationLog->updatePaginationLog(
						$this->getLogTypeId(),
						['manual_status' => 0]
					);
				}
				/* Manual Sync Tracking */
			} else {
				/* info log */
				$msg = 'Something has wrong occured. Please check error.log';
				echo $msg . "<br/>";
				$this->intProductHelper->createLogFiles('Response => ' . json_encode($response), 'INFO', $this->intProductHelper::PRODUCT_REQUEST_RESPONSE_LOG, 'request-response.log', $this->getWebsiteId());
				$this->intProductHelper->createLogFiles($msg . "\n", 'INFO', $this->intProductHelper::PRODUCT_DUMP_LOG, 'error.log', $this->getWebsiteId());

				/* Manual Sync Tracking */
				if ($this->getMode() == \Integration\ManualSync\Helper\Data::MANUAL_MODE) {
					$maualSyncCronWatch = $this->manualSyncWatch->getLastRunningRecord(
						$this->getLogTypeId(),
						\Integration\ManualSync\Model\Status::SYNC_RUNNING_STATUS,
						$this->getWebsiteId()
					);

					if ($maualSyncCronWatch && $maualSyncCronWatch->getId()) {
						$this->manualSyncWatch->setId($maualSyncCronWatch->getId())->setStatus('error')->setErrorMessage($msg)->save();
					}
				}
				/* Manual Sync Tracking */
			}
		} catch (\Exception $e) {
			/* critical log */
			$this->intProductHelper->createLogFiles('Response => ' . json_encode($response), 'CRIT', $this->intProductHelper::PRODUCT_DUMP_LOG, 'error.log', $this->getWebsiteId());
			$this->intProductHelper->createLogFiles('Error => ' . $e->getMessage(), 'CRIT', $this->intProductHelper::PRODUCT_DUMP_LOG, 'error.log', $this->getWebsiteId());
			/* critical log */

			/* Manual Sync Tracking */
			if ($this->getMode() == \Integration\ManualSync\Helper\Data::MANUAL_MODE) {
				$maualSyncCronWatch = $this->manualSyncWatch->getLastRunningRecord(
					$this->getLogTypeId(),
					\Integration\ManualSync\Model\Status::SYNC_RUNNING_STATUS,
					$this->getWebsiteId()
				);

				if ($maualSyncCronWatch && $maualSyncCronWatch->getId()) {
					$this->manualSyncWatch->setId($maualSyncCronWatch->getId())->setStatus('error')->setErrorMessage($e->getMessage())->save();
				}
			}
			/* Manual Sync Tracking */
		}

		return $this;
	}
}
