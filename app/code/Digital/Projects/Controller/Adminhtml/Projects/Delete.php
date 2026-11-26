<?php
namespace Digital\Projects\Controller\Adminhtml\Projects;

use Digital\Projects\Controller\Adminhtml\AbstractProjects;

class Delete extends AbstractProjects
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
            /** @var \Digital\Projects\Model\Projects $model */
            $model = $this->projectsFactory->create();
            try {
                $model->load($id);
                $model->delete();
                $this->messageManager->addSuccessMessage(__('The projects has been deleted.'));
                return $resultRedirect->setPath('*/*/');
            } catch (\Exception $e) {
                $this->messageManager->addErrorMessage(__('Something went wrong while deleted the projects.'));
                $this->_objectManager->get(\Psr\Log\LoggerInterface::class)->critical($e->getMessage());
            }
        }

        return $resultRedirect->setPath('*/*/');
    }
}
