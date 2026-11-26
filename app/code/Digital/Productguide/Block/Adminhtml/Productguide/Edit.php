<?php
namespace Digital\Productguide\Block\Adminhtml\Productguide;

/**
 * Admin Productguide page
 *
 */
class Edit extends \Magento\Backend\Block\Widget\Form\Container
{
    /**
     * Admin Productguide page
     *
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry = null;

    /**
     * @param \Magento\Backend\Block\Widget\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Widget\Context $context,
        \Magento\Framework\Registry $registry,
        array $data = []
    ) {
        $this->_coreRegistry = $registry;
        parent::__construct($context, $data);
    }

    /**
     * Initialize cms page edit block
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_objectId = 'productguide_id';
        $this->_blockGroup = 'Digital_Productguide';
        $this->_controller = 'adminhtml_productguide';

        parent::_construct();

        $this->buttonList->remove('save');
        $this->buttonList->remove('saveandcontinue');
        $this->buttonList->remove('reset');

        if ($this->_isAllowedAction('Digital_Productguide::delete')) {
            $this->buttonList->update('delete', 'label', __('Delete'));
        } else {
            $this->buttonList->remove('delete');
        }
    }

    /**
     * Retrieve text for header element depending on loaded page
     *
     * @return string
     */
    public function getHeaderText()
    {
        if ($this->_coreRegistry->registry('productguide')->getId()) {
            return __("Edit Product guide '%1'", $this->escapeHtml(
                $this->_coreRegistry->registry('productguide')->getName()
            ));
        } else {
            return __('New Product guide');
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
