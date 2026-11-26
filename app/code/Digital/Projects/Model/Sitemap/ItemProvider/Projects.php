<?php
declare(strict_types=1);

namespace Digital\Projects\Model\Sitemap\ItemProvider;

use Digital\Projects\Model\ResourceModel\Projects\CollectionFactory;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Sitemap\Model\ItemProvider\ConfigReaderInterface;
use Magento\Sitemap\Model\ItemProvider\ItemProviderInterface;
use Magento\Sitemap\Model\SitemapItemInterfaceFactory;
use Magento\Store\Model\ScopeInterface;

/**
 * Feeds Digital_Projects entities into Magento's XML sitemap generator.
 *
 * URLs returned here MUST be relative to the store base URL.
 * Magento\Sitemap\Model\Sitemap::_getSitemapRow() prepends the store base URL.
 */
class Projects implements ItemProviderInterface
{
    /**
     * Frontend route prefix used by Digital\Projects\Controller\Router
     */
    private const URL_PREFIX = 'projects';

    private const XML_PATH_SITEMAP_ENABLED = 'projects_section/digital_projects/enabled';
    private const XML_PATH_INCLUDE_LISTING = 'projects_section/digital_projects/include_listing';

    public function __construct(
        private CollectionFactory $collectionFactory,
        private SitemapItemInterfaceFactory $itemFactory,
        private ConfigReaderInterface $configReader,
        private ScopeConfigInterface $scopeConfig
    ) {
    }

    /**
     * @param int $storeId
     * @return \Magento\Sitemap\Model\SitemapItemInterface[]
     */
    public function getItems($storeId)
    {
        if (!$this->scopeConfig->isSetFlag(
            self::XML_PATH_SITEMAP_ENABLED,
            ScopeInterface::SCOPE_STORE,
            $storeId
        )) {
            return [];
        }

        $priority = $this->configReader->getPriority($storeId);
        $changeFrequency = $this->configReader->getChangeFrequency($storeId);

        $items = [];

        if ($this->scopeConfig->isSetFlag(
            self::XML_PATH_INCLUDE_LISTING,
            ScopeInterface::SCOPE_STORE,
            $storeId
        )) {
            $items[] = $this->itemFactory->create([
                'url' => self::URL_PREFIX,
                'priority' => $priority,
                'changeFrequency' => $changeFrequency,
            ]);
        }

        /** @var \Digital\Projects\Model\ResourceModel\Projects\Collection $collection */
        $collection = $this->collectionFactory->create();
        $collection->addActiveStatusFilter()
            ->addFieldToFilter('status', ['eq' => 1])
            ->addFieldToFilter('projects_url', ['neq' => '']);        
        foreach ($collection as $project) {
            $urlKey = trim((string)$project->getData('projects_url'), '/');
            if ($urlKey === '') {
                continue;
            }

            $items[] = $this->itemFactory->create([
                'url' => self::URL_PREFIX . '/' . $urlKey,
                'updatedAt' => $project->getData('updated_at'),
                'priority' => $priority,
                'changeFrequency' => $changeFrequency,
            ]);
        }

        return $items;
    }
}
