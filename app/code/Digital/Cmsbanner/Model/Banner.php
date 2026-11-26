<?php

declare(strict_types=1);

namespace Digital\Cmsbanner\Model;

use Digital\Cmsbanner\Api\Data\BannerInterface;
use Magento\Framework\Model\AbstractModel;

class Banner extends AbstractModel implements BannerInterface
{
    protected $_eventPrefix = 'digital_cmsbanner';

    protected function _construct(): void
    {
        $this->_init(\Digital\Cmsbanner\Model\ResourceModel\Banner::class);
    }

    public function getBannerId(): ?int
    {
        $id = $this->getData(self::BANNER_ID);
        return $id !== null ? (int)$id : null;
    }

    public function getName(): string
    {
        return (string)$this->getData(self::NAME);
    }

    public function getLink(): ?string
    {
        return $this->getData(self::LINK);
    }

    public function getStatus(): int
    {
        return (int)$this->getData(self::STATUS);
    }

    public function getSortOrder(): int
    {
        return (int)$this->getData(self::SORT_ORDER);
    }

    public function getImage(): ?string
    {
        return $this->getData(self::IMAGE);
    }

    public function getMobileImage(): ?string
    {
        return $this->getData(self::MOBILE_IMAGE);
    }

    public function getCmsPages(): ?string
    {
        return $this->getData(self::CMS_PAGES);
    }

    public function getCreatedAt(): ?string
    {
        return $this->getData(self::CREATED_AT);
    }

    public function getUpdatedAt(): ?string
    {
        return $this->getData(self::UPDATED_AT);
    }

    public function setBannerId(int $bannerId): self
    {
        return $this->setData(self::BANNER_ID, $bannerId);
    }

    public function setName(string $name): self
    {
        return $this->setData(self::NAME, $name);
    }

    public function setLink(?string $link): self
    {
        return $this->setData(self::LINK, $link);
    }

    public function setStatus(int $status): self
    {
        return $this->setData(self::STATUS, $status);
    }

    public function setSortOrder(int $sortOrder): self
    {
        return $this->setData(self::SORT_ORDER, $sortOrder);
    }

    public function setImage(?string $image): self
    {
        return $this->setData(self::IMAGE, $image);
    }

    public function setMobileImage(?string $image): self
    {
        return $this->setData(self::MOBILE_IMAGE, $image);
    }

    public function setCmsPages(?string $cmsPages): self
    {
        return $this->setData(self::CMS_PAGES, $cmsPages);
    }

    public function getAvailableStatuses(): array
    {
        return [
            self::STATUS_ENABLED  => __('Enabled'),
            self::STATUS_DISABLED => __('Disabled'),
        ];
    }
}
