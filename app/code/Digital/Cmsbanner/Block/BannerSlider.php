<?php

declare(strict_types=1);

namespace Digital\Cmsbanner\Block;

use Digital\Cmsbanner\Api\Data\BannerInterface;
use Digital\Cmsbanner\Model\ResourceModel\Banner\Collection;
use Digital\Cmsbanner\Model\ResourceModel\Banner\CollectionFactory;
use Magento\Cms\Model\Page;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\UrlInterface;
use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;
use Magento\Store\Model\StoreManagerInterface;

class BannerSlider extends Template
{
    protected $_template = 'Digital_Cmsbanner::banner_slider.phtml';

    public function __construct(
        Context $context,
        private readonly CollectionFactory $bannerCollectionFactory,
        private readonly Page $cmsPage,
        array $data = []
    ) {
        parent::__construct($context, $data);
    }

    public function getBanners(): Collection
    {
        $currentPageIdentifier = $this->getCurrentCmsPageIdentifier();

        /** @var Collection $collection */
        $collection = $this->bannerCollectionFactory->create();
        $collection->addFieldToFilter('status', BannerInterface::STATUS_ENABLED);
        $collection->setOrder('sort_order', 'ASC');

        // Filter by cms_pages
        $allBanners = $collection->getItems();
        $filteredBanners = [];

        foreach ($allBanners as $banner) {
            $cmsPages = $banner->getCmsPages();

            // Show if no CMS pages restriction OR if current page matches
            if (empty($cmsPages)) {
                $filteredBanners[] = $banner;
            } elseif ($currentPageIdentifier) {
                $allowedPages = array_map('trim', explode(',', $cmsPages));
                if (in_array($currentPageIdentifier, $allowedPages)) {
                    $filteredBanners[] = $banner;
                }
            }
        }

        // Return filtered collection
        $filteredCollection = $this->bannerCollectionFactory->create();
        $filteredCollection->addFieldToFilter('status', BannerInterface::STATUS_ENABLED);
        $filteredCollection->setOrder('sort_order', 'ASC');

        if ($currentPageIdentifier) {
            $filteredCollection->getSelect()->where(
                'cms_pages IS NULL OR cms_pages = "" OR FIND_IN_SET(?, cms_pages)',
                $currentPageIdentifier
            );
        } else {
            $filteredCollection->addFieldToFilter([
                ['attribute' => 'cms_pages', 'null' => true],
                ['attribute' => 'cms_pages', 'eq' => ''],
            ]);
        }

        return $filteredCollection;
    }

    public function getCurrentCmsPageIdentifier(): ?string
    {
        try {
            $identifier = $this->cmsPage->getIdentifier();
            return $identifier ?: null;
        } catch (\Exception $e) {
            return null;
        }
    }

    public function getCurrentCmsPageTitle(): ?string
    {
        try {
            return $this->cmsPage->getTitle() ?: null;
        } catch (\Exception $e) {
            return null;
        }
    }

    public function getImageUrl(?string $imageName): string
    {
        if (empty($imageName)) {
            return '';
        }
        try {
            $baseUrl = $this->_storeManager->getStore()->getBaseUrl(UrlInterface::URL_TYPE_MEDIA);
            return $baseUrl . 'digital/cmsbanner/' . $imageName;
        } catch (LocalizedException $e) {
            return '';
        }
    }

    public function getCheckdevice()
    {
        $iphone = strpos($_SERVER['HTTP_USER_AGENT'],"iPhone");
        $android = strpos($_SERVER['HTTP_USER_AGENT'],"Android");
        $mobile = strpos($_SERVER['HTTP_USER_AGENT'],"Mobile");
        $palmpre = strpos($_SERVER['HTTP_USER_AGENT'],"webOS");
        $berry = strpos($_SERVER['HTTP_USER_AGENT'],"BlackBerry");
        $ipod = strpos($_SERVER['HTTP_USER_AGENT'],"iPod");
        $device="1";
        if ($iphone || ($android && $mobile) || $palmpre || $ipod || $berry == true)
        {
            $device= "2";
        }
        return $device;
        
    }
}
