<?php

namespace Digital\Productguide\Controller\Adminhtml\Index;

class Grid extends \Magento\Customer\Controller\Adminhtml\Index
{
    /**
     * Customer grid action
     *
     * @return void
     */
    public function execute()
    {
        /*@codingStandardsIgnoreStart*/
        $this->_view->loadLayout(false);
        $this->_view->renderLayout();
        /*@codingStandardsIgnoreEnd*/
    }
}
