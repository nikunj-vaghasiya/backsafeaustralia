<?php
namespace Digital\Homeboxes\Controller\Adminhtml\Boxes;

use Digital\Homeboxes\Controller\Adminhtml\AbstractBoxes;

class Index extends AbstractBoxes
{

    /**
     * Execute
     *
     * @return void
     */

    /* @codingStandardsIgnoreStart */
    public function execute()
    {
        $this->_initAction()->_addBreadcrumb(__('Content'), __('Content'));
        $this->_view->getPage()->getConfig()->getTitle()->prepend(__('Manage Boxes'));
        $this->_view->renderLayout();
    }
    /* @codingStandardsIgnoreEnd */
}
