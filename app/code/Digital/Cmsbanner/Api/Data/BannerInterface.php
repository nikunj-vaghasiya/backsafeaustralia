<?php

declare(strict_types=1);

namespace Digital\Cmsbanner\Api\Data;

interface BannerInterface
{
    public const BANNER_ID  = 'banner_id';
    public const NAME       = 'name';
    public const LINK       = 'link';
    public const STATUS     = 'status';
    public const SORT_ORDER = 'sort_order';
    public const IMAGE      = 'image';
    public const MOBILE_IMAGE      = 'mobile_image';
    public const CMS_PAGES  = 'cms_pages';
    public const CREATED_AT = 'created_at';
    public const UPDATED_AT = 'updated_at';

    public const STATUS_ENABLED  = 1;
    public const STATUS_DISABLED = 0;

    public function getBannerId(): ?int;
    public function getName(): string;
    public function getLink(): ?string;
    public function getStatus(): int;
    public function getSortOrder(): int;
    public function getImage(): ?string;
    public function getMobileImage(): ?string;
    public function getCmsPages(): ?string;
    public function getCreatedAt(): ?string;
    public function getUpdatedAt(): ?string;

    public function setBannerId(int $bannerId): self;
    public function setName(string $name): self;
    public function setLink(?string $link): self;
    public function setStatus(int $status): self;
    public function setSortOrder(int $sortOrder): self;
    public function setImage(?string $image): self;
    public function setMobileImage(?string $image): self;
    public function setCmsPages(?string $cmsPages): self;
}
