<?php
namespace Digital\Homeboxes\Block;

use Digital\Base\Helper\Data as baseHelper;
use Digital\Homeboxes\Block\AbstractBoxes;
use Digital\Homeboxes\Helper\Data as boxHelper;
use Digital\Homeboxes\Model\ResourceModel\Boxes\CollectionFactory as BoxesCollectionFactory;
use Magento\Framework\View\Element\Template as Template;

class Boxes extends AbstractBoxes
{
    /**
     * Slider constructor.
     *
     * @param Template\Context        $context
     * @param BoxesCollectionFactory  $boxesCollectionFactory
     * @param Basehelper              $baseHelper
     * @param Boxhelper               $boxHelper
     * @param array                   $data
     */

    /* @codingStandardsIgnoreStart */
    public function __construct(
        Template\Context $context,
        BoxesCollectionFactory $boxesCollectionFactory,
        baseHelper $baseHelper,
        boxHelper $boxHelper,
        array $data = []
    ) {
        parent::__construct(
            $context,
            $boxesCollectionFactory,
            $baseHelper,
            $boxHelper,
            $data
        );
    }
    /* @codingStandardsIgnoreEnd */
}
