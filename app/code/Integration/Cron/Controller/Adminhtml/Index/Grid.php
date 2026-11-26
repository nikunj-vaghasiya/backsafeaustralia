<?php

namespace Integration\Cron\Controller\Adminhtml\Index;

class Grid extends \Magento\Customer\Controller\Adminhtml\Index
{
    /**
     * Customer grid action
     *
     * @return void
     */
    protected function _isAllowed()
    {
        return true;
    }

    public function execute()
    {
        $this->_view->loadLayout(false);
        $this->_view->renderLayout();
    }
}
