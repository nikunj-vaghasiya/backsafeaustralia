<?php
namespace Digital\BannerSlider\Block;

use Digital\BannerSlider\Block\AbstractSlider;
use Digital\BannerSlider\Helper\Data as BannerHelper;
use Digital\BannerSlider\Model\ResourceModel\Banner\CollectionFactory as BannerCollectionFactory;
use Digital\Base\Helper\Data as BaseHelper;
use Magento\Framework\View\Element\Template as Template;
use Magento\Widget\Model\Template\Filter;

class Slider extends AbstractSlider
{
    /**
     * @var PageFactory
     */
    private $pageFactory;

    /**
     * Slider constructor.
     *
     * @param Template\Context        $context
     * @param BannerCollectionFactory $bannerCollectionFactory
     * @param PageFactory             $baseHelper
     * @param array                   $data
     */
    /*@codingStandardsIgnoreStart*/
    public function __construct(
        Template\Context $context,
        BannerCollectionFactory $bannerCollectionFactory,
        BaseHelper $baseHelper,
        BannerHelper $bannerHelper,
        Filter $templateProcessor,
        array $data = []
    ) {
        parent::__construct(
            $context,
            $bannerCollectionFactory,
            $baseHelper,
            $bannerHelper,
            $templateProcessor,
            $data
        );
    }
    /*@codingStandardsIgnoreEnd*/
}
