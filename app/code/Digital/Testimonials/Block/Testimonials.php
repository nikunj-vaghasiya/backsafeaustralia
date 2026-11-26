<?php

namespace Digital\Testimonials\Block;

class Testimonials extends \Magento\Framework\View\Element\Template
{
    protected $_testimonials;
    protected $_testimonialsHelper;
    protected function _prepareLayout()
    {
        parent::_prepareLayout();
        /* $this->pageConfig->getTitle()->set(__('What Our Client Says')); */

        if ($this->getTestimonials()) {
            $pager = $this->getLayout()->createBlock(
                \Magento\Theme\Block\Html\Pager::class,
                'fme.news.pager'
            )->setAvailableLimit($this->getAvailableLimit())->setShowPerPage(true)->setCollection(
                $this->getTestimonials()
            );
            $this->setChild('pager', $pager);
            $this->getTestimonials()->load();
        }
        return $this;
    }

    public function __construct(
        \Magento\Catalog\Block\Product\Context $context,
        \Digital\Testimonials\Model\Testimonials $testimonials,
        \Digital\Testimonials\Helper\Data $testimonialsHelper,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->_testimonials = $testimonials;
        $this->_testimonialsHelper = $testimonialsHelper;
    }

    /*check module is enabled or not for frontend*/
    public function isEnabled()
    {
        return $this->_testimonialsHelper->isEnabled();
    }

    /*get collection with pagination filter data*/
    public function getTestimonials()
    {
        $page=($this->getRequest()->getParam('p'))? $this->getRequest()->getParam('p') : 1;
        $pageSize=($this->getRequest()->getParam('limit'))?
        $this->getRequest()->getParam('limit') : $this->getDefaultPageSize();
        $testimonialsCollection = $this->_testimonials->getCollection();
        $testimonialsCollection->addFieldToFilter('status', \Digital\Testimonials\Model\Status::STATUS_ENABLED);
        $testimonialsCollection->setOrder('main_table.sortorder', 'ASC')
        ->setOrder('testimonial_id', 'DESC');
        $testimonialsCollection->setCurPage($page);
        $testimonialsCollection->setPageSize($pageSize);
        return $testimonialsCollection;
    }

    public function getHomeTestimonials()
    {
        $testimonialsCollection = $this->_testimonials->getCollection();
        $testimonialsCollection->addFieldToFilter('status', \Digital\Testimonials\Model\Status::STATUS_ENABLED);
        $testimonialsCollection->setOrder('main_table.sortorder', 'ASC')
        ->setOrder('testimonial_id', 'DESC');
        return $testimonialsCollection;
    }

    /*This is use for check how many records are save in db*/

    public function getAllTestimonialCount()
    {
        $testimonialsCollection = $this->_testimonials->getCollection();
        $testimonialsCollection->addFieldToSelect('*')
                                ->addFieldToFilter('status', \Digital\Testimonials\Model\Status::STATUS_ENABLED)
                                ->setOrder('main_table.sortorder', 'ASC')
                                ->setOrder('testimonial_id', 'DESC');
        return count($testimonialsCollection);
    }

    /* show per page recors in frontend*/
    public function getDefaultPageSize()
    {
        return $this->_testimonialsHelper->getPerPageRecords();
    }

    /*make toolbar limiter here*/
    public function getAvailableLimit()
    {
        $perPageRecords = $this->getDefaultPageSize();
        return [
            $perPageRecords => $perPageRecords,
            $perPageRecords+$perPageRecords => $perPageRecords+$perPageRecords,
            $perPageRecords+($perPageRecords*2) =>$perPageRecords+($perPageRecords*2),
            $perPageRecords+($perPageRecords*3) =>$perPageRecords+($perPageRecords*3)
        ];
    }

    public function getPagerHtml()
    {
        $allCount = $this->getAllTestimonialCount();
        $defaultCount = $this->getDefaultPageSize();
        if ($allCount > $defaultCount) {
            return $this->getChildHtml('pager');
        }
    }
}
