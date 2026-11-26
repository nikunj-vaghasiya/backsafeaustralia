<?php

declare(strict_types=1);

namespace Digital\Cmsbanner\Controller\Adminhtml\Banner;

use Digital\Cmsbanner\Api\BannerRepositoryInterface;
use Magento\Backend\App\Action\Context;
use Magento\Framework\App\Action\HttpGetActionInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\View\Result\PageFactory;

class Edit extends AbstractBanner implements HttpGetActionInterface
{
    public const ADMIN_RESOURCE = 'Digital_Cmsbanner::banner_save';

    public function __construct(
        Context $context,
        private readonly PageFactory $resultPageFactory,
        private readonly BannerRepositoryInterface $bannerRepository
    ) {
        parent::__construct($context);
    }

    public function execute()
    {
        $bannerId = (int)$this->getRequest()->getParam('banner_id');

        if ($bannerId) {
            try {
                $banner = $this->bannerRepository->getById($bannerId);
                $title = __('Edit Banner: %1', $banner->getName());
            } catch (NoSuchEntityException $e) {
                $this->messageManager->addErrorMessage(__('This banner no longer exists.'));
                return $this->resultRedirectFactory->create()->setPath('*/*/');
            }
        } else {
            $title = __('New Banner');
        }

        $resultPage = $this->resultPageFactory->create();
        $resultPage->setActiveMenu('Digital_Cmsbanner::cmsbanner');
        $resultPage->getConfig()->getTitle()->prepend($title);
        return $resultPage;
    }
}
