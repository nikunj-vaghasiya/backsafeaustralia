<?php

namespace Integration\Cron\Block\Adminhtml;

class Cron extends \Magento\Backend\Block\Template
{
	const IMPORT_PRODUCT_CRON_TIME = 5;
	const IMPORT_CUSTOMER_CRON_TIME = 5;
	const IMPORT_GUEST_CUSTOMER_PRICING_CRON_TIME = 5;
	const IMPORT_LOGGED_IN_CUSTOMER_PRICING_CRON_TIME = 5;
	const ADD_BATCH_BUFFER = 2;

	public function __construct(
		\Magento\Backend\Block\Template\Context $context,
		\Magento\Framework\App\ResourceConnection $resource,
		\Integration\Cron\Model\Buffer $cronBuffer,
		\Integration\ManualSync\Helper\Data $manualHelper
	) {
		parent::__construct($context);
		$this->_resource = $resource;
		$this->cronBuffer = $cronBuffer;
		$this->manualHelper = $manualHelper;
		$this->_connection = $resource->getConnection();
	}


	public function getRunningScripts($storeId = 1)
	{
		try {
			$websiteId = $this->manualHelper->getWebsiteIdByStoreId($storeId);
			$status = \Integration\ManualSync\Model\Status::SYNC_RUNNING_STATUS;
			$runningEntities = array();
			$records = $this->cronBuffer->getCollection()
				->addFieldToFilter('status', ['eq' => $status])
				->addFieldToFilter('website_id', ['eq' => $websiteId]);

			$customerTypeId = ($websiteId != 1) ? \Integration\ManualSync\Helper\Data::NZ_CUSTOMER_IMPORT_BATCH_TYPE_ID : \Integration\ManualSync\Helper\Data::CUSTOMER_IMPORT_BATCH_TYPE_ID;

			$productTypeId = ($websiteId != 1) ? \Integration\ManualSync\Helper\Data::NZ_PRODUCT_IMPORT_BATCH_TYPE_ID : \Integration\ManualSync\Helper\Data::PRODUCT_IMPORT_BATCH_TYPE_ID;

			$customerPricingTypeId = ($websiteId != 1) ? \Integration\ManualSync\Helper\Data::NZ_CUSTOMER_PRICING_BATCH_TYPE_ID : \Integration\ManualSync\Helper\Data::CUSTOMER_PRICING_BATCH_TYPE_ID;

			$customerDynamicPricingTypeId = ($websiteId != 1) ? \Integration\ManualSync\Helper\Data::NZ_CUSTOMER_DYNAMIC_PRICING_BATCH_TYPE_ID : \Integration\ManualSync\Helper\Data::CUSTOMER_DYNAMIC_PRICING_BATCH_TYPE_ID;

			if ($records && $records->getSize()) {
				foreach ($records as $record) {
					if (isset($record['type']) && $record['type'] == $customerTypeId) {
						$cutomerImportTimeText = "";
						if ($cutomerImportTime = $this->getCustomerImportRemainingTime(
							$customerTypeId,
							$status,
							\Integration\ManualSync\Helper\Data::AUTO_MODE
						)) {
							$cutomerImportTimeText = "(take approx " . $cutomerImportTime . " Minutes)";
						}
						$runningEntities[] = 'Customer' . $cutomerImportTimeText;
					} else if (isset($record['type']) && $record['type'] == $productTypeId) {
						$productImportTimeText = "";
						if ($productImportTime = $this->getProductImportRemainingTime(
							$productTypeId,
							$status,
							\Integration\ManualSync\Helper\Data::AUTO_MODE
						)) {
							$productImportTimeText = "(take approx " . $productImportTime . " Minutes)";
						}
						$runningEntities[] = 'Product' . $productImportTimeText;
					} else if (isset($record['type']) && $record['type'] == $customerPricingTypeId) {
						$cpImportTimeText = "";
						if ($productImportTime = $this->getProductImportRemainingTime(
							$customerPricingTypeId,
							$status,
							\Integration\ManualSync\Helper\Data::AUTO_MODE
						)) {
							$cpImportTimeText = "(take approx " . $productImportTime . " Minutes)";
						}
						$runningEntities[] = 'Customer Pricing' . $cpImportTimeText;
					} else if (isset($record['type']) && $record['type'] == $customerDynamicPricingTypeId) {
						$priceImportTimeText = "";
						if ($productImportTime = $this->getProductImportRemainingTime(
							$customerDynamicPricingTypeId,
							$status,
							\Integration\ManualSync\Helper\Data::AUTO_MODE
						)) {
							$priceImportTimeText = "(take approx " . $productImportTime . " Minutes)";
						}
						$runningEntities[] = 'Customer Specific Pricing' . $priceImportTimeText;
					}
				}
			}
			if (count($runningEntities)) {
				return implode(' and ', $runningEntities);/*array_unique($runningEntities);*/
			}

			return false;
		} catch (Exception $e) {
			return false;
		}
	}

	public function getCustomerImportRemainingTime($type, $status, $mode = \Integration\ManualSync\Helper\Data::AUTO_MODE, $websiteId = 1)
	{
		try {
			$cronBuffer = $this->cronBuffer->getCollection()
				->addFieldToFilter('type', ['eq' => $type])
				->addFieldToFilter('status', ['eq' => $status])
				->addFieldToFilter('mode', ['eq' => $mode])
				->addFieldToFilter('website_id', ['eq' => $websiteId]);

			if ($cronBuffer && $cronBuffer->getSize() && $mode == \Integration\ManualSync\Helper\Data::AUTO_MODE) {
				$count = $cronBuffer->getSize();
				return (int)(($count + self::ADD_BATCH_BUFFER) * self::IMPORT_CUSTOMER_CRON_TIME);
			} else {
				$count = $cronBuffer->getSize();
				return (int)$count;
			}
			return false;
		} catch (Exception $e) {
			return false;
		}
	}

	public function getProductImportRemainingTime($type, $status, $mode = \Integration\ManualSync\Helper\Data::AUTO_MODE, $websiteId = 1)
	{
		try {
			$cronBuffer = $this->cronBuffer->getCollection()
				->addFieldToFilter('type', ['eq' => $type])
				->addFieldToFilter('status', ['eq' => $status])
				->addFieldToFilter('mode', ['eq' => $mode])
				->addFieldToFilter('website_id', ['eq' => $websiteId]);

			if ($cronBuffer && $cronBuffer->getSize() && $mode == \Integration\ManualSync\Helper\Data::AUTO_MODE) {
				$count = $cronBuffer->getSize();
				return (int)(($count + self::ADD_BATCH_BUFFER) * self::IMPORT_PRODUCT_CRON_TIME);
			} else {
				$count = $cronBuffer->getSize();
				return (int)$count;
			}
			return false;
		} catch (Exception $e) {
			return false;
		}
	}

	public function getGuestCustomerPriceImportRemainingTime($type, $status, $mode = \Integration\ManualSync\Helper\Data::AUTO_MODE, $websiteId = 1)
	{
		try {
			$cronBuffer = $this->cronBuffer->getCollection()
				->addFieldToFilter('type', ['eq' => $type])
				->addFieldToFilter('status', ['eq' => $status])
				->addFieldToFilter('mode', ['eq' => $mode])
				->addFieldToFilter('website_id', ['eq' => $websiteId]);

			if ($cronBuffer && $cronBuffer->getSize() && $mode == \Integration\ManualSync\Helper\Data::AUTO_MODE) {
				$count = $cronBuffer->getSize();
				return (int)(($count + self::ADD_BATCH_BUFFER) * self::IMPORT_LOGGED_IN_CUSTOMER_PRICING_CRON_TIME);
			} else {
				$count = $cronBuffer->getSize();
				return (int)$count;
			}
			return false;
		} catch (Exception $e) {
			return false;
		}
	}

	public function getLoggedInCustomerPriceImportRemainingTime($type, $status, $mode = \Integration\ManualSync\Helper\Data::AUTO_MODE, $websiteId = 1)
	{
		try {
			$cronBuffer = $this->cronBuffer->getCollection()
				->addFieldToFilter('type', ['eq' => $type])
				->addFieldToFilter('status', ['eq' => $status])
				->addFieldToFilter('mode', ['eq' => $mode])
				->addFieldToFilter('website_id', ['eq' => $websiteId]);

			if ($cronBuffer && $cronBuffer->getSize() && $mode == \Integration\ManualSync\Helper\Data::AUTO_MODE) {
				$count = $cronBuffer->getSize();
				return (int)(($count + self::ADD_BATCH_BUFFER) * self::IMPORT_LOGGED_IN_CUSTOMER_PRICING_CRON_TIME);
			} else {
				$count = $cronBuffer->getSize();
				return (int)$count;
			}
			return false;
		} catch (Exception $e) {
			return false;
		}
	}
}
