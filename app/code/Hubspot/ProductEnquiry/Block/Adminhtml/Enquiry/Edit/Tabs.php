<?php
namespace Hubspot\ProductEnquiry\Block\Adminhtml\Enquiry\Edit;

class Tabs extends \Magento\Backend\Block\Widget\Tabs
{
    protected function _construct()
    {
        parent::_construct();
        $this->setId('productenquiry_enquiry_edit_tabs');
        $this->setDestElementId('edit_form');
        $this->setTitle(__('Enquiry Information'));
    }
}
