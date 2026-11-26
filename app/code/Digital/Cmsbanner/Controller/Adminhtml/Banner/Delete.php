<?php

declare(strict_types=1);

namespace Digital\Cmsbanner\Controller\Adminhtml\Banner;

use Digital\Cmsbanner\Api\BannerRepositoryInterface;
use Magento\Backend\App\Action\Context;
use Magento\Framework\App\Action\HttpPostActionInterface;
use Magento\Framework\Exception\LocalizedException;

class Delete extends AbstractBanner implements HttpPostActionInterface
{
    public const ADMIN_RESOURCE = 'Digital_Cmsbanner::banner_delete';

    public function __construct(
        Context $context,
        private readonly BannerRepositoryInterface $bannerRepository
    ) {
        parent::__construct($context);
    }

    public function execute()
    {
        $resultRedirect = $this->resultRedirectFactory->create();
        $bannerId = (int)$this->getRequest()->getParam('banner_id');

        if (!$bannerId) {
            $this->messageManager->addErrorMessage(__('We can\'t find a banner to delete.'));
            return $resultRedirect->setPath('*/*/');
        }

        try {
            $this->bannerRepository->deleteById($bannerId);
            $this->messageManager->addSuccessMessage(__('Banner has been deleted.'));
        } catch (LocalizedException $e) {
            $this->messageManager->addErrorMessage($e->getMessage());
        } catch (\Exception $e) {
            $this->messageManager->addErrorMessage(__('Something went wrong while deleting the banner.'));
        }

        return $resultRedirect->setPath('*/*/');
    }
}
