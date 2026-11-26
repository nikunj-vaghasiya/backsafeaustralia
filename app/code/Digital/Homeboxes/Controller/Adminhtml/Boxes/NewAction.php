<?php

namespace Digital\Homeboxes\Controller\Adminhtml\Boxes;

use Digital\Homeboxes\Controller\Adminhtml\AbstractBoxes;

class NewAction extends AbstractBoxes
{

    /**
     * New Boxes action
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
