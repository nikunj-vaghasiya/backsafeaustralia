<?php

declare(strict_types=1);

namespace Digital\Cmsbanner\Api;

use Digital\Cmsbanner\Api\Data\BannerInterface;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;

interface BannerRepositoryInterface
{
    /**
     * @throws CouldNotSaveException
     */
    public function save(BannerInterface $banner): BannerInterface;

    /**
     * @throws NoSuchEntityException
     */
    public function getById(int $bannerId): BannerInterface;

    /**
     * @throws CouldNotDeleteException
     */
    public function delete(BannerInterface $banner): bool;

    /**
     * @throws CouldNotDeleteException
     * @throws NoSuchEntityException
     */
    public function deleteById(int $bannerId): bool;
}
