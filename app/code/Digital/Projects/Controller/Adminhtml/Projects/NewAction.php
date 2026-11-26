<?php
namespace Digital\Projects\Controller\Adminhtml\Projects;

use Digital\Projects\Controller\Adminhtml\AbstractProjects;

class NewAction extends AbstractProjects
{

    /**
     * New Projects action
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
