<?php
/**
 * Adminhtml Productguide list block
 *
 */
namespace Digital\Productguide\Block\Adminhtml;

class Productguide extends \Magento\Backend\Block\Widget\Grid\Container
{
    /**
     * Constructor
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_controller = 'adminhtml_productguide';
        $this->_blockGroup = 'Digital_Productguide';
        $this->_headerText = __('Productguide');
        parent::_construct();
        
        $this->buttonList->remove('add');
    }

    /**
     * Check permission for passed action
     *
     * @param string $resourceId
     * @return bool
     */
    protected function _isAllowedAction($resourceId)
    {
        return $this->_authorization->isAllowed($resourceId);
    }
}
