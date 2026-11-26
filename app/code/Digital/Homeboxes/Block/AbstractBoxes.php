<?php
namespace Digital\Homeboxes\Block;

use Digital\Homeboxes\Model\ResourceModel\Boxes\CollectionFactory as BoxesCollectionFactory;
use Magento\Framework\View\Element\Template as Template;

abstract class AbstractBoxes extends Template
{
    /**
     * @var BoxesCollectionFactory
     */
    protected $boxesCollectionFactory;

    /**
     * @var Collection
     */
    protected $boxesCollection;

    /**
     * AbstractSlider constructor.
     *
     * @param Template\Context        $context
     * @param BoxesCollectionFactory  $boxesCollectionFactory
     * @param Basehelper              $baseHelper
     * @param Boxhelper               $boxHelper
     * @param array                   $data
     */
    public function __construct(
        Template\Context $context,
        BoxesCollectionFactory $boxesCollectionFactory,
        \Digital\Base\Helper\Data $baseHelper,
        \Digital\Homeboxes\Helper\Data $boxHelper,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->boxesCollectionFactory = $boxesCollectionFactory;
        $this->baseHelper = $baseHelper;
        $this->boxHelper = $boxHelper;
    }

    /**
     * Get boxes collection
     *
     * @return boolean|Collection
     */
    public function getBoxesCollection()
    {
        if ($this->boxesCollection === null) {

            $fullActionName = $this->getRequest()->getFullActionName();

            $boxesCollection = $this->boxesCollectionFactory->create();
            $boxesCollection->addActiveStatusFilter();
            $boxesCollection->addStoreFilter($this->baseHelper->getStoreId());
            if($fullActionName == 'cms_index_index')
            {
                $boxesCollection->addFieldToFilter('showinhomepage', 1);
            }
            else
            {
                $boxesCollection->addFieldToFilter('showinminingpage', 1);
            }
            $boxesCollection->setOrder('sort_order, boxes_id', "ASC DESC");
            $boxesCollection->getSelect()->limit($this->boxHelper->getHomeboxesCollectionLimit());
            $this->boxesCollection = $boxesCollection;
        }

        return $this->boxesCollection;
    }

    /**
     * To Html
     *
     * @return string
     */
    protected function _toHtml()
    {
        if ($this->getBoxesCollection() && $this->getBoxesCollection()->getSize() > 0) {
            return parent::_toHtml();
        }

        return '';
    }

    /**
     * To Html
     *
     * @return string
     */
    public function getBaseHelper()
    {
        return $this->baseHelper;
    }

    /**
     * To Html
     *
     * @return string
     */
    public function getBoxHelper()
    {
        return $this->boxHelper;
    }
}
