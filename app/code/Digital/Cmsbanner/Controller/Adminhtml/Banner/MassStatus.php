<?php

declare(strict_types=1);

namespace Digital\Cmsbanner\Controller\Adminhtml\Banner;

use Digital\Cmsbanner\Api\BannerRepositoryInterface;
use Digital\Cmsbanner\Api\Data\BannerInterface;
use Digital\Cmsbanner\Model\ResourceModel\Banner\CollectionFactory;
use Magento\Backend\App\Action\Context;
use Magento\Framework\App\Action\HttpPostActionInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Ui\Component\MassAction\Filter;

class MassStatus extends AbstractBanner implements HttpPostActionInterface
{
    public const ADMIN_RESOURCE = 'Digital_Cmsbanner::banner_save';

    public function __construct(
        Context $context,
        private readonly Filter $filter,
        private readonly CollectionFactory $collectionFactory,
        private readonly BannerRepositoryInterface $bannerRepository
    ) {
        parent::__construct($context);
    }

    public function execute()
    {
        $resultRedirect = $this->resultRedirectFactory->create();
        $status = (int)$this->getRequest()->getParam('status');

        if (!in_array($status, [BannerInterface::STATUS_ENABLED, BannerInterface::STATUS_DISABLED])) {
            $this->messageManager->addErrorMessage(__('Invalid status value.'));
            return $resultRedirect->setPath('*/*/');
        }

        try {
            $collection = $this->filter->getCollection($this->collectionFactory->create());
            $updated = 0;

            foreach ($collection->getItems() as $banner) {
                $banner->setStatus($status);
                $this->bannerRepository->save($banner);
                $updated++;
            }

            $label = $status === BannerInterface::STATUS_ENABLED ? __('Enabled') : __('Disabled');
            $this->messageManager->addSuccessMessage(
                __('A total of %1 banner(s) have been %2.', $updated, $label)
            );
        } catch (LocalizedException $e) {
            $this->messageManager->addErrorMessage($e->getMessage());
        } catch (\Exception $e) {
            $this->messageManager->addErrorMessage(__('Something went wrong during mass status update.'));
        }

        return $resultRedirect->setPath('*/*/');
    }
}
