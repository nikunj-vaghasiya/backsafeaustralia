<?php

namespace Digital\BannerSlider\Block;

use Digital\BannerSlider\Model\ResourceModel\Banner\Collection;
use Digital\BannerSlider\Model\ResourceModel\Banner\CollectionFactory as BannerCollectionFactory;
use Magento\Framework\View\Element\Template as Template;

abstract class AbstractSlider extends Template
{
    /**
     * @var BannerCollectionFactory
     */
    protected $bannerCollectionFactory;

    /**
     * @var Collection
     */
    protected $bannerCollection;
    protected $templateProcessor;

    /**
     * AbstractSlider constructor.
     *
     * @param Template\Context                     $context
     * @param BannerCollectionFactory              $bannerCollectionFactory
     * @param \Digital\Base\Helper\Data            $baseHelper
     * @param \Digital\BannerSlider\Helper\Data    $bannerHelper
     * @param array                                $data
     */
    public function __construct(
        Template\Context $context,
        BannerCollectionFactory $bannerCollectionFactory,
        \Digital\Base\Helper\Data $baseHelper,
        \Digital\BannerSlider\Helper\Data $bannerHelper,
        \Magento\Widget\Model\Template\Filter $templateProcessor,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->bannerCollectionFactory = $bannerCollectionFactory;
        $this->baseHelper = $baseHelper;
        $this->bannerHelper = $bannerHelper;
        $this->templateProcessor = $templateProcessor;
    }

    /**
     * Get banner collection
     *
     * @return boolean|Collection
     */
    public function getBannerCollection()
    {
        if ($this->bannerCollection === null) {
            $bannerCollection = $this->bannerCollectionFactory->create();
            $bannerCollection->addActiveStatusFilter();
            $bannerCollection->addStoreFilter($this->baseHelper->getStoreId());
            $bannerCollection->addOrder('sort_order', 'ASC');
            $bannerCollection->addOrder('banner_id', 'ASC');
            $bannerCollection->getSelect()->limit(10);
            $this->bannerCollection = $bannerCollection;
        }

        return $this->bannerCollection;
    }

    /**
     * Get Base Helper
     *
     * @return boolean|Collection
     */
    public function getBaseHelper()
    {
        return $this->baseHelper;
    }

    /**
     * Get Banner Helper
     *
     * @return boolean|Collection
     */
    public function getBannerHelper()
    {
        return $this->bannerHelper;
    }

    /**
     * To Html
     *
     * @return string
     */
    protected function _toHtml()
    {
        if ($this->getBannerCollection() && $this->getBannerCollection()->getSize() > 0) {
            return parent::_toHtml();
        }

        return '';
    }

    public function filterOutputHtml($string)
    {
        return $this->templateProcessor->filter($string);
    }
}
