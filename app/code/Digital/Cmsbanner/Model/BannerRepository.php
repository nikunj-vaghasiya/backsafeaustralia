<?php

declare(strict_types=1);

namespace Digital\Cmsbanner\Model;

use Digital\Cmsbanner\Api\BannerRepositoryInterface;
use Digital\Cmsbanner\Api\Data\BannerInterface;
use Digital\Cmsbanner\Model\ResourceModel\Banner as BannerResource;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;

class BannerRepository implements BannerRepositoryInterface
{
    public function __construct(
        private readonly BannerFactory $bannerFactory,
        private readonly BannerResource $bannerResource
    ) {}

    public function save(BannerInterface $banner): BannerInterface
    {
        try {
            $this->bannerResource->save($banner);
        } catch (\Exception $e) {
            throw new CouldNotSaveException(__('Could not save banner: %1', $e->getMessage()));
        }
        return $banner;
    }

    public function getById(int $bannerId): BannerInterface
    {
        $banner = $this->bannerFactory->create();
        $this->bannerResource->load($banner, $bannerId);
        if (!$banner->getId()) {
            throw new NoSuchEntityException(__('Banner with ID "%1" does not exist.', $bannerId));
        }
        return $banner;
    }

    public function delete(BannerInterface $banner): bool
    {
        try {
            $this->bannerResource->delete($banner);
        } catch (\Exception $e) {
            throw new CouldNotDeleteException(__('Could not delete banner: %1', $e->getMessage()));
        }
        return true;
    }

    public function deleteById(int $bannerId): bool
    {
        return $this->delete($this->getById($bannerId));
    }
}
