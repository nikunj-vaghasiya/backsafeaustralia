<?php

namespace Integration\Product\Helper;

use Integration\Base\Model\Config\Source\FilterOptions;

class Data extends \Integration\Base\Helper\AbstractHelper
{
	/**
     * Store manager
     *
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * Currently selected store ID if applicable
     *
     * @var int
     */
    protected $_storeId;

    const XML_API_ENABLE_SYNC_PRODUCT = 'api_product/general/sync_enable';

    const XML_API_ENABLE_MANUAL_SYNC_PRODUCT = 'api_product/general/manual_sync_enable';

    const XML_API_ENDPOINT_URL_PRODUCT = 'base/api_url/product';

    const XML_API_FILER_DATE = 'api_product/general/filter_options';

    const XML_API_SYNC_DATE = 'api_product/general/date_filter';

    const XML_API_SPECIFIC_DATE = 'api_product/general/specific_date';

    const XML_API_PAGINATION_ENABLE = 'api_product/general/pagination_enable';

    const XML_API_PAGINATION_PAGE_SIZE = 'api_product/general/page_size';

    const PRODUCT_REQUEST_RESPONSE_LOG = 'product/';

    const PRODUCT_DUMP_LOG = 'product/dump/';

    const PRODUCT_BATCH_LOG = 'product/batch/';

    const PRODUCT_IMPORT_LOG = 'product/import/';

    /**
     * Is product entity enable or disable
     *
     * @param int $storeId Store view ID
     * @return bool
     */
    public function isProductApiEnabled($storeId = null)
    {
    	return $this->isEntityEnabled(self::XML_API_ENABLE_SYNC_PRODUCT, $storeId);
    }

    /**
     * Is manual sync product entity enable or disable
     *
     * @param int $storeId Store view ID
     * @return bool
     */
    public function isManualProductApiEnabled($storeId = null)
    {
        $isEnabled = $this->isProductApiEnabled($storeId);
        if(!$isEnabled) {
            return $isEnabled;
        }

        return $this->isEntityEnabled(self::XML_API_ENABLE_MANUAL_SYNC_PRODUCT, $storeId);
    }

    /**
     * Returns product API url value from configuration
     *
     * @param int $websiteId
     * @return string
     */
    public function getProductAPIEndpointUrl($websiteId = null)
    {
    	return $this->getAPIEndpointUrl(self::XML_API_ENDPOINT_URL_PRODUCT,$websiteId);
    }

    /**
     * Returns product API request filter date
     *
     * @param int $websiteId
     * @return string|bool
     */
    public function getProductRequestFilterDate($websiteId = null)
    {
        return $this->getRequestFilterDate(
                    $this->isProductApiEnabled(),
                    self::XML_API_FILER_DATE,
                    self::XML_API_SYNC_DATE,
                    self::XML_API_SPECIFIC_DATE,
                    $websiteId
                );
    }

    /**
     * Returns product API url
     *
     * @return string
     */
    public function getProductAPIUrl()
    {
        $params = ['_expand' => ['VendorDetails','Attributes','UOMConversions']];
        if($this->getProductRequestFilterDate()){
            $filterByDateVal = $this->getFilterByDate(self::XML_API_FILER_DATE);
            if($filterByDateVal && in_array($filterByDateVal,[FilterOptions::CURRENT_PREVIOUS_DATE,FilterOptions::SPECIFIC_DATE])){
                $params['_filter'] = ['and' => ['ge' => ['LastModified' => 'datetimeoffset\''.$this->getProductRequestFilterDate().'\'']]];
            }
        }
        return $this->buildAPIUrl($this->getProductAPIEndpointUrl(),$params);
    }

    public function isPaginationEnabled($websiteId = null)
    {
        if(!$websiteId) {
            $websiteId = $this->_storeManager->getStore()->getWebsiteId();
        }

        return $this->scopeConfig->isSetFlag(
            self::XML_API_PAGINATION_ENABLE,
            \Magento\Store\Model\ScopeInterface::SCOPE_WEBSITE,
            $websiteId
        );
    }

    public function getPageSize($websiteId = null)
    {
        if(!$websiteId) {
            $websiteId = $this->_storeManager->getStore()->getWebsiteId();
        }

        $pageSize = $this->scopeConfig->getValue(
            self::XML_API_PAGINATION_PAGE_SIZE,
            \Magento\Store\Model\ScopeInterface::SCOPE_WEBSITE,
            $websiteId
        );

        return ($pageSize) ? trim($pageSize) : 0;
    }

    public function getPreviousDateAndTime()
    {
        $currentTimezone = date_default_timezone_get();
        date_default_timezone_set('Australia/Melbourne');
        $date = date('Y-m-d H:i:s',strtotime("-365 days"));
        date_default_timezone_set($currentTimezone);
        return $date;
    }

    public function changeDateTimeFormat($date,$format='Y-m-d H:i:s')
    {
        return date($format,strtotime($date));
    }
}
