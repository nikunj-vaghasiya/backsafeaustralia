<?php
namespace Digital\Ourteam\Controller\Adminhtml\Team;

use Digital\Ourteam\Controller\Adminhtml\AbstractTeam;

class Delete extends AbstractTeam
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
            /** @var \Digital\Ourteam\Model\Ourteam $model */
            $model = $this->ourteamFactory->create();
            try {
                $model->load($id);
                $model->delete();
                $this->messageManager->addSuccessMessage(__('The Team has been deleted.'));
                return $resultRedirect->setPath('*/*/');
            } catch (\Exception $e) {
                $this->messageManager->addErrorMessage(__('Something went wrong while deleted the Team.'));
                $this->_objectManager->get(\Psr\Log\LoggerInterface::class)->critical($e->getMessage());
            }
        }

        return $resultRedirect->setPath('*/*/');
    }
}