<?php
namespace Digital\BannerSlider\Controller\Adminhtml\Banner;

use Digital\BannerSlider\Controller\Adminhtml\AbstractBanner;

class Delete extends AbstractBanner
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
            /** @var \Digital\BannerSlider\Model\Banner $model */
            $model = $this->bannerFactory->create();
            try {
                $model->load($id);
                $model->delete();
                $this->messageManager->addSuccessMessage(__('The banner has been deleted.'));
                return $resultRedirect->setPath('*/*/');
            } catch (\Exception $e) {
                $this->messageManager->addErrorMessage(__('Something went wrong while deleted the banner.'));
                $this->_objectManager->get(\Psr\Log\LoggerInterface::class)->critical($e->getMessage());
            }
        }

        return $resultRedirect->setPath('*/*/');
    }
}
