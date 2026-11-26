<?php
namespace Digital\Productguide\Controller\Adminhtml\Index;

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
     * Check the permission to run it
     *
     * @return bool
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Digital_Productguide::manage');
    }

    /**
     * Productguide List action
     *
     * @return void
     */

    public function execute()
    {
        /** @var \Magento\Backend\Model\View\Result\Page $resultPage */

        $resultPage = $this->resultPageFactory->create();
        $resultPage->setActiveMenu(
            'Digital_Productguide::manage'
        )->addBreadcrumb(
            __('Product guide'),
            __('Product guide')
        )->addBreadcrumb(
            __('Manage Product guide'),
            __('Manage Product guide')
        );

        $resultPage->getConfig()->getTitle()->prepend(__('Product Guide Enquiries'));
        return $resultPage;
    }
}
