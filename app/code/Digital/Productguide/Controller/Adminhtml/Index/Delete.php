<?php

namespace Digital\Productguide\Controller\Adminhtml\Index;

class Delete extends \Magento\Backend\App\Action
{
    /**
     * Allow Delete to Users
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Digital_Productguide::delete');
    }

    /**
     * Delete action
     *
     * @return void
     */
    public function execute()
    {
        // check if we know what should be deleted
        $id = $this->getRequest()->getParam('productguide_id');
        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();
        if ($id) {
            $title = "";
            try {
                // init model and delete
                $model = $this->_objectManager->create(\Digital\Productguide\Model\Productguide::class);
                $model->load($id);
                $title = $model->getTitle();
                $model->delete();
                // display success message
                $this->messageManager->addSuccess(__('The Productguide Enquiry has been deleted.'));
                // go to grid
                return $resultRedirect->setPath('*/*/');
            } catch (\Exception $e) {
                // display error message
                $this->messageManager->addError($e->getMessage());
                // go back to edit form
                return $resultRedirect->setPath('*/*/edit', ['page_id' => $id]);
            }
        }
        // display error message
        $this->messageManager->addError(__('We can\'t find a data to delete.'));
        // go to grid
        return $resultRedirect->setPath('*/*/');
    }
}
