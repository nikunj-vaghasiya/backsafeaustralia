<?php
namespace Hubspot\ProductEnquiry\Block\Adminhtml\Enquiry;

/**
 * Product Enquiry view container (native admin form container with header + buttons).
 */
class Edit extends \Magento\Backend\Block\Widget\Form\Container
{
    /** @var \Magento\Framework\Registry */
    protected $_coreRegistry = null;

    public function __construct(
        \Magento\Backend\Block\Widget\Context $context,
        \Magento\Framework\Registry $registry,
        array $data = []
    ) {
        $this->_coreRegistry = $registry;
        parent::__construct($context, $data);
    }

    protected function _construct()
    {
        $this->_objectId = 'entity_id';
        $this->_blockGroup = 'Hubspot_ProductEnquiry';
        $this->_controller = 'adminhtml_enquiry';

        parent::_construct();

        // Read-only view: no save / save-and-continue / reset.
        $this->buttonList->remove('save');
        $this->buttonList->remove('saveandcontinue');
        $this->buttonList->remove('reset');

        // Keep Back (default) and Delete.
        if ($this->buttonList->getItems()) {
            $this->buttonList->update('delete', 'label', __('Delete'));
        }
    }

    public function getHeaderText()
    {
        $enquiry = $this->_coreRegistry->registry('current_enquiry');
        $id = $enquiry ? $enquiry->getId() : '';
        return __("View Enquiry #%1", $id);
    }
}
