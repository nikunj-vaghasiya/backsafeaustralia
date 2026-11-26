<?php
namespace Digital\Testimonials\Helper;
 
class Data extends \Magento\Framework\App\Helper\AbstractHelper
{
    public const XML_PATH_ENABLED = 'digital_testimonials/general/enable';
    public const XML_PATH_PER_PAGE_RECORD = 'digital_testimonials/general/per_page';
    public const DEFAULT_RECORDS = 10;
    
    public function isEnabled()
    {
        return $this->scopeConfig->isSetFlag(
            self::XML_PATH_ENABLED,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    public function getPerPageRecords()
    {
        $recordPerPage = $this->scopeConfig->getValue(
            self::XML_PATH_PER_PAGE_RECORD,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
        if ($recordPerPage && $recordPerPage != 0) {
            return $recordPerPage;
        }
        return self::DEFAULT_RECORDS;
    }
}
