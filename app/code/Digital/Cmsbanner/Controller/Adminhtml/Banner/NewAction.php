<?php

declare(strict_types=1);

namespace Digital\Cmsbanner\Controller\Adminhtml\Banner;

use Magento\Backend\App\Action\Context;
use Magento\Framework\App\Action\HttpGetActionInterface;
use Magento\Framework\View\Result\PageFactory;

class NewAction extends AbstractBanner implements HttpGetActionInterface
{
    public const ADMIN_RESOURCE = 'Digital_Cmsbanner::banner_save';

    public function __construct(
        Context $context,
        private readonly PageFactory $resultPageFactory
    ) {
        parent::__construct($context);
    }

    public function execute()
    {
        $resultPage = $this->resultPageFactory->create();
        $resultPage->setActiveMenu('Digital_Cmsbanner::cmsbanner');
        $resultPage->getConfig()->getTitle()->prepend(__('New Banner'));
        return $resultPage;
    }
}
