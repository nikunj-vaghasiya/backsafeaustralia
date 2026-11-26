<?php
namespace Hubspot\ProductEnquiry\Controller\Adminhtml\Enquiry;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Registry;
use Magento\Framework\View\Result\PageFactory;
use Hubspot\ProductEnquiry\Model\EnquiryFactory;

class View extends Action
{
    const ADMIN_RESOURCE = 'Hubspot_ProductEnquiry::enquiry';

    /** @var PageFactory */
    protected $resultPageFactory;

    /** @var Registry */
    protected $registry;

    /** @var EnquiryFactory */
    protected $enquiryFactory;

    public function __construct(
        Context $context,
        PageFactory $resultPageFactory,
        Registry $registry,
        EnquiryFactory $enquiryFactory
    ) {
        parent::__construct($context);
        $this->resultPageFactory = $resultPageFactory;
        $this->registry = $registry;
        $this->enquiryFactory = $enquiryFactory;
    }

    public function execute()
    {
        $id = (int) $this->getRequest()->getParam('entity_id');
        $model = $this->enquiryFactory->create();
        if ($id) {
            $model->load($id);
        }

        if (!$model->getId()) {
            $this->messageManager->addErrorMessage(__('This enquiry no longer exists.'));
            return $this->resultRedirectFactory->create()->setPath('*/*/');
        }

        $this->registry->register('current_enquiry', $model);

        $resultPage = $this->resultPageFactory->create();
        $resultPage->setActiveMenu(self::ADMIN_RESOURCE);
        $resultPage->getConfig()->getTitle()->prepend(__('View Enquiry #%1', $model->getId()));
        return $resultPage;
    }
}
