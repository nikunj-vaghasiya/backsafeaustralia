<?php
namespace Digital\Homeboxes\Controller\Adminhtml\Boxes;

use Digital\Homeboxes\Controller\Adminhtml\AbstractBoxes;
use Magento\Framework\Exception\LocalizedException;

class Save extends AbstractBoxes
{
    /**
     * Execute
     *
     * @return $this|\Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\ResultInterface
     * @throws LocalizedException
     */
    public function execute()
    {
        $data = $this->getRequest()->getPostValue();

        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();
        if ($data) {
            /** @var \Digital\Homeboxes\Model\Boxes $model */
            $model = $this->boxesFactory->create();
            if (!empty($data['boxes_id'])) {
                $model->load($data['boxes_id']);
                if (!$model->getId()) {
                    throw new LocalizedException(__('Wrong boxes Id: %1.', $data['boxes_id']));
                }
            }
            $model->addData($data);
            $this->_objectManager->get(\Magento\Backend\Model\Session::class)->setPageData($model->getData());
            try {
                $this->_eventManager->dispatch(
                    'controller_action_digital_boxes_save_entity_before',
                    ['controller' => $this, 'boxes' => $model]
                );
                $model->save();
                $this->messageManager->addSuccessMessage(__('The boxes has been saved.'));
                $this->_objectManager->get(\Magento\Backend\Model\Session::class)->setPageData(false);
                if ($this->getRequest()->getParam('back')) {
                    return $resultRedirect->setPath('*/*/edit', ['id' => $model->getId(), '_current' => true]);
                }

                return $resultRedirect->setPath('*/*/');
            } catch (\Exception $e) {
                $this->messageManager->addErrorMessage(__($e->getMessage()));
                $this->_objectManager->get(\Psr\Log\LoggerInterface::class)->critical($e->getMessage());
                return $resultRedirect->setPath('*/*/edit', ['id' => $this->getRequest()->getParam('id')]);
            }
        }
        return $resultRedirect->setPath('*/*/');
    }
}
