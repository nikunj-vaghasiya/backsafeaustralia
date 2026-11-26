<?php
namespace Hubspot\ProductEnquiry\Block\Adminhtml\Enquiry;

use Magento\Backend\Block\Template;
use Magento\Backend\Block\Template\Context;
use Magento\Framework\Registry;
use Hubspot\ProductEnquiry\Model\Enquiry;

class View extends Template
{
    /** @var Registry */
    protected $registry;

    public function __construct(Context $context, Registry $registry, array $data = [])
    {
        $this->registry = $registry;
        parent::__construct($context, $data);
    }

    /**
     * @return Enquiry|null
     */
    public function getEnquiry()
    {
        return $this->registry->registry('current_enquiry');
    }

    public function getBackUrl(): string
    {
        return $this->getUrl('*/*/');
    }

    public function getDeleteUrl(): string
    {
        $enquiry = $this->getEnquiry();
        return $this->getUrl('*/*/delete', ['entity_id' => $enquiry ? $enquiry->getId() : null]);
    }
}
