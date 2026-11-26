<?php
namespace Digital\Homeboxes\Controller\Adminhtml\Boxes;

use Digital\Homeboxes\Controller\Adminhtml\AbstractBoxes;

class Delete extends AbstractBoxes
{

    /**
     * Execute
     *
     * @return $this|\Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        $id = (int) $this->getRequest()->getParam('id');
        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();
        if ($id) {
            /** @var \Digital\Homeboxes\Model\Boxes $model */
            $model = $this->boxesFactory->create();
            try {
                $model->load($id);
                $model->delete();
                $this->messageManager->addSuccessMessage(__('The boxes has been deleted.'));
                return $resultRedirect->setPath('*/*/');
            } catch (\Exception $e) {
                $this->messageManager->addErrorMessage(__('Something went wrong while deleted the boxes.'));
                $this->_objectManager->get(\Psr\Log\LoggerInterface::class)->critical($e->getMessage());
            }
        }

        return $resultRedirect->setPath('*/*/');
    }
}
