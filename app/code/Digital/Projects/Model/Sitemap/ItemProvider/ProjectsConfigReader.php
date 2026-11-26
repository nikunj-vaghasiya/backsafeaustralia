<?php
declare(strict_types=1);

namespace Digital\Projects\Model\Sitemap\ItemProvider;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Sitemap\Model\ItemProvider\ConfigReaderInterface;
use Magento\Store\Model\ScopeInterface;

/**
 * Reads Stores > Configuration > Catalog > XML Sitemap > Projects Options
 */
class ProjectsConfigReader implements ConfigReaderInterface
{
    private const XML_PATH_CHANGE_FREQUENCY = 'sitemap/digital_projects/changefreq';
    private const XML_PATH_PRIORITY = 'sitemap/digital_projects/priority';

    public function __construct(
        private ScopeConfigInterface $scopeConfig
    ) {
    }

    /**
     * @param int $storeId
     * @return string
     */
    public function getPriority($storeId)
    {
        return (string)$this->scopeConfig->getValue(
            self::XML_PATH_PRIORITY,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    /**
     * @param int $storeId
     * @return string
     */
    public function getChangeFrequency($storeId)
    {
        return (string)$this->scopeConfig->getValue(
            self::XML_PATH_CHANGE_FREQUENCY,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }
}
