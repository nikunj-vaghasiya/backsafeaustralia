<?php

namespace Digital\BannerSlider\Controller\Adminhtml\Banner;

use Digital\BannerSlider\Controller\Adminhtml\AbstractBanner;

class Index extends AbstractBanner
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
        $this->_view->getPage()->getConfig()->getTitle()->prepend(__('Manage Banner'));
        $this->_view->renderLayout();
    }
    /* @codingStandardsIgnoreEnd */
}
