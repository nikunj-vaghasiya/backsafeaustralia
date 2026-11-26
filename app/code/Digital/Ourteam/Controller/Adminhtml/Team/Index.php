<?php
namespace Digital\Ourteam\Controller\Adminhtml\Team;

use Digital\Ourteam\Controller\Adminhtml\AbstractTeam;

class Index extends AbstractTeam
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
        $this->_view->getPage()->getConfig()->getTitle()->prepend(__('Manage Team'));
        $this->_view->renderLayout();
    }
    /* @codingStandardsIgnoreEnd */
}?>