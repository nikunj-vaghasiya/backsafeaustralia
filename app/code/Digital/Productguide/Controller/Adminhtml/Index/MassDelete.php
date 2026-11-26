<?php
namespace Digital\Productguide\Controller\Adminhtml\Index;

class MassDelete extends \Magento\Backend\App\Action
{
    /**
     * Forward to Delete
     *
     * @var \Magento\Framework\View\Result\PageFactory
     */
    public function execute()
    {
        $ids = $this->getRequest()->getParam('productguide');
        $resultRedirect = $this->resultRedirectFactory->create();
        if (!is_array($ids) || empty($ids)) {
            $this->messageManager->addError(__('Please select item(s).'));
        } else {
            try {
                foreach ($ids as $id) {
                    $row = $this->_objectManager->get(\Digital\Productguide\Model\Productguide::class)->load($id);
                    $row->delete();
                }
                $this->messageManager->addSuccess(
                    __('A total of %1 record(s) have been deleted.', count($ids))
                );
            } catch (\Exception $e) {
                $this->messageManager->addError($e->getMessage());
            }
        }
        return $resultRedirect->setPath('*/*/');
    }
}
