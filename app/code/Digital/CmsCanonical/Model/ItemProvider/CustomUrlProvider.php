<?php
namespace Digital\CmsCanonical\Model\ItemProvider;

use Magento\Sitemap\Model\ItemProvider\ItemProviderInterface;
use Magento\Sitemap\Model\SitemapItemFactory;
use Mageplaza\Blog\Model\ResourceModel\Post\CollectionFactory as PostCollectionFactory;
use Mageplaza\Blog\Helper\Data as BlogHelper;

class CustomUrlProvider implements ItemProviderInterface
{
    private $sitemapItemFactory;
    private $postCollectionFactory;
    private $blogHelper;

    private const URL_PREFIX = 'news';
    private const URL_PREFIX_OUR_TEAM = 'our-team';

    public function __construct(
        SitemapItemFactory $sitemapItemFactory,
        PostCollectionFactory $postCollectionFactory,
        BlogHelper $blogHelper
    ) {
        $this->sitemapItemFactory = $sitemapItemFactory;
        $this->postCollectionFactory = $postCollectionFactory;
        $this->blogHelper = $blogHelper;
    }

    public function getItems($storeId)
    {
        $sitemapItems = [];

        $collection = $this->postCollectionFactory->create();
        $collection->addFieldToFilter('enabled', 1);

         $sitemapItems[] = $this->sitemapItemFactory->create([
                'url' => self::URL_PREFIX,
                'priority' => '0.5',
                'changeFrequency' => 'weekly'
            ]);

        $sitemapItems[] = $this->sitemapItemFactory->create([
                'url' => self::URL_PREFIX_OUR_TEAM,
                'priority' => '0.5',
                'changeFrequency' => 'weekly'
            ]);

        foreach ($collection as $post) {
            $urlKey = $post['url_key'];
            $sitemapItems[] = $this->sitemapItemFactory->create([
                'url' => self::URL_PREFIX . '/' . $urlKey,
                'updatedAt' => $post->getUpdatedAt(),                
                'priority' => '0.5',
                'changeFrequency' => 'weekly'
            ]);
        }

       

        return $sitemapItems;
    }
}