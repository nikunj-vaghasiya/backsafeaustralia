<?php

namespace Digital\Testimonials\Controller\Adminhtml\testimonials;

use Magento\Backend\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;

class Index extends \Magento\Backend\App\Action
{
    /**
     * @var PageFactory
     */
    protected $resultPagee;
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
    public function execute()
    {
        /** @var \Magento\Backend\Model\View\Result\Page $resultPage */
        $resultPage = $this->resultPageFactory->create();
        $resultPage->setActiveMenu('Digital_Testimonials::testimonials');
        $resultPage->addBreadcrumb(__('Digital'), __('Digital'));
        $resultPage->addBreadcrumb(__('Manage Testimonials'), __('Manage Testimonials'));
        $resultPage->getConfig()->getTitle()->prepend(__('Manage Testimonials'));

        return $resultPage;
    }
}
