<?php
namespace Digital\Projects\Controller\Adminhtml\Projects;

use Digital\Projects\Controller\Adminhtml\AbstractProjects;

class Index extends AbstractProjects
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
        $this->_view->getPage()->getConfig()->getTitle()->prepend(__('Manage Projects'));
        $this->_view->renderLayout();
    }
    /* @codingStandardsIgnoreEnd */
}
