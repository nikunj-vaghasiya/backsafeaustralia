<?php

namespace Digital\CmsCanonical\Observer;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\View\Page\Config as PageConfig;
use Magento\Framework\App\Request\Http;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Cms\Model\Page as CmsPage;

class AddCmsCanonical implements ObserverInterface
{
    public function __construct(
        private PageConfig $pageConfig,
        private Http $request,
        private StoreManagerInterface $storeManager,
        private CmsPage $cmsPage
    ) {}

    public function execute(Observer $observer): void
    {
        // Only apply on CMS pages
        $fullActionName = $this->request->getFullActionName();
        $baseUrl = $this->storeManager->getStore()->getBaseUrl();
        if ($fullActionName === 'mpblog_post_index') {
            $canonicalUrl = $baseUrl.'news';
            $this->pageConfig->addRemotePageAsset(
                $canonicalUrl,
                'canonical',
                ['attributes' => ['rel' => 'canonical']]
            );
        }

        if ($fullActionName !== 'cms_page_view' && $fullActionName !== 'cms_index_index') {
            return;
        }

        $pageIdentifier = $this->cmsPage->getIdentifier();

        if (!$pageIdentifier) {
            return;
        }

        // Handle homepage edge case
        if ($fullActionName === 'cms_index_index') {
            $canonicalUrl = $baseUrl;
        } else {
            $canonicalUrl = rtrim($baseUrl, '/') . '/' . ltrim($pageIdentifier, '/');
        }

        $this->pageConfig->addRemotePageAsset(
            $canonicalUrl,
            'canonical',
            ['attributes' => ['rel' => 'canonical']]
        );
    }
}
