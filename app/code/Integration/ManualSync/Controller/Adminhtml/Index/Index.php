<?php
namespace Integration\ManualSync\Controller\Adminhtml\Index;

use Magento\Backend\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;

class Index extends \Magento\Backend\App\Action
{
    /**
     * @var PageFactory
     */
    protected $resultPageFactory;

    /**
     * @param Context $context
     * @param PageFactory $resultPageFactory
     */
    public function __construct(
        Context $context,
        PageFactory $resultPageFactory
    ) {
        parent::__construct($context);
        $this->resultPageFactory = $resultPageFactory;
    }

    /**
     * Index action
     *
     * @return void
     */
    protected function _isAllowed()
    {
     return $this->_authorization->isAllowed('Integration_ManualSync::manual');
    }

    public function execute()
    {
        /** @var \Magento\Backend\Model\View\Result\Page $resultPage */
        $resultPage = $this->resultPageFactory->create();
        $resultPage->setActiveMenu('Integration_ManualSync::manual');
        $resultPage->addBreadcrumb(__('Integration'), __('Integration'));
        $resultPage->addBreadcrumb(__('Manual Sync Manager'), __('Manual Sync Manager'));
        $resultPage->getConfig()->getTitle()->prepend(__('Manual Sync Manager'));

        return $resultPage;
    }
}
?>
