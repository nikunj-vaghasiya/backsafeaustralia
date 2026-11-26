<?php
namespace Digital\Productenquiry\Controller\Adminhtml\Index;

use Magento\Backend\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;

class Index extends \Magento\Backend\App\Action
{
    protected $resultPageFactory;

    public function __construct(
        Context $context,
        PageFactory $resultPageFactory
    ) {
        parent::__construct($context);
        $this->resultPageFactory = $resultPageFactory;
    }

    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Digital_Productenquiry::manage');
    }

    public function execute()
    {
        $resultPage = $this->resultPageFactory->create();
        $resultPage->setActiveMenu(
            'Digital_Productenquiry::manage'
        )->addBreadcrumb(
            __('Quote Requests'),
            __('Quote Requests')
        )->addBreadcrumb(
            __('Manage Quote Requests'),
            __('Manage Quote Requests')
        );

        $resultPage->getConfig()->getTitle()->prepend(__('Quote Requests'));
        return $resultPage;
    }
}
