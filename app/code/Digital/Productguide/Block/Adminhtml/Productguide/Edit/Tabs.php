<?php
namespace Digital\Productguide\Block\Adminhtml\Productguide\Edit;

/**
 * Admin Productguide left menu
 */
class Tabs extends \Magento\Backend\Block\Widget\Tabs
{
    /**
     * Productguide Block tab
     *
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setId('page_tabs');
        $this->setDestElementId('edit_form');
        $this->setTitle(__('Product guide Enquiry Information'));
    }
}
