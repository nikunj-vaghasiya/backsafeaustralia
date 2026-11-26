<?php

namespace Digital\BannerSlider\Controller\Adminhtml\Banner;

use Digital\BannerSlider\Controller\Adminhtml\AbstractBanner;

class NewAction extends AbstractBanner
{

    /**
     * New Banner action
     *
     * @return void
     */
    /* @codingStandardsIgnoreStart */
    public function execute()
    {
        $this->_forward('edit');
    }
    /* @codingStandardsIgnoreEnd */
}
