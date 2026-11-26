<?php

namespace Digital\Ourteam\Controller\Adminhtml\Team;

use Digital\Ourteam\Controller\Adminhtml\AbstractTeam;

class NewAction extends AbstractTeam
{

    /**
     * New Tean action
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
