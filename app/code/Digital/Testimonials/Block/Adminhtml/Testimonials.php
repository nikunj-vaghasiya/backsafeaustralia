<?php
/**
 * Adminhtml warehouse list block
 *
 */
namespace Digital\Testimonials\Block\Adminhtml;

class Testimonials extends \Magento\Backend\Block\Widget\Grid\Container
{
    /**
     * Constructor
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_controller = 'adminhtml_testimonials';
        $this->_blockGroup = 'Digital_Testimonials';
        $this->_headerText = __('Testimonials');
        $this->_addButtonLabel = __('Add New Testimonials');
        parent::_construct();
        if ($this->_isAllowedAction('Digital_Testimonials::save')) {
            $this->buttonList->update('add', 'label', __('Add New Testimonials'));
        } else {
            $this->buttonList->remove('add');
        }
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
