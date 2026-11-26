<?php

declare(strict_types=1);

namespace Digital\Cmsbanner\Model\ResourceModel\Banner;

use Digital\Cmsbanner\Model\Banner;
use Digital\Cmsbanner\Model\ResourceModel\Banner as BannerResource;
use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

class Collection extends AbstractCollection
{
    protected $_idFieldName = 'banner_id';

    protected function _construct(): void
    {
        $this->_init(Banner::class, BannerResource::class);
    }
}
