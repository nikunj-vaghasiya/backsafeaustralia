<?php
namespace Hubspot\ProductEnquiry\Controller\Adminhtml\Enquiry;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Hubspot\ProductEnquiry\Model\EnquiryFactory;

class Delete extends Action
{
    const ADMIN_RESOURCE = 'Hubspot_ProductEnquiry::enquiry';

    /** @var EnquiryFactory */
    protected $enquiryFactory;

    public function __construct(Context $context, EnquiryFactory $enquiryFactory)
    {
        parent::__construct($context);
        $this->enquiryFactory = $enquiryFactory;
    }

    public function execute()
    {
        $resultRedirect = $this->resultRedirectFactory->create();
        $id = (int) $this->getRequest()->getParam('entity_id');

        if ($id) {
            try {
                $model = $this->enquiryFactory->create();
                $model->load($id);
                if (!$model->getId()) {
                    $this->messageManager->addErrorMessage(__('This enquiry no longer exists.'));
                } else {
                    $model->delete();
                    $this->messageManager->addSuccessMessage(__('The enquiry has been deleted.'));
                }
            } catch (\Exception $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
            }
        }

        return $resultRedirect->setPath('*/*/');
    }
}
