<?php

declare(strict_types=1);

namespace Digital\Cmsbanner\Controller\Adminhtml\Banner;

use Digital\Cmsbanner\Api\BannerRepositoryInterface;
use Digital\Cmsbanner\Model\ResourceModel\Banner\CollectionFactory;
use Magento\Backend\App\Action\Context;
use Magento\Framework\App\Action\HttpPostActionInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Ui\Component\MassAction\Filter;

class MassDelete extends AbstractBanner implements HttpPostActionInterface
{
    public const ADMIN_RESOURCE = 'Digital_Cmsbanner::banner_delete';

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

        try {
            $collection = $this->filter->getCollection($this->collectionFactory->create());
            $deleted = 0;

            foreach ($collection->getItems() as $banner) {
                $this->bannerRepository->delete($banner);
                $deleted++;
            }

            $this->messageManager->addSuccessMessage(__('A total of %1 banner(s) have been deleted.', $deleted));
        } catch (LocalizedException $e) {
            $this->messageManager->addErrorMessage($e->getMessage());
        } catch (\Exception $e) {
            $this->messageManager->addErrorMessage(__('Something went wrong during mass delete.'));
        }

        return $resultRedirect->setPath('*/*/');
    }
}
