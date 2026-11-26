<?php

declare(strict_types=1);

namespace Digital\Cmsbanner\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

class Banner extends AbstractDb
{
    protected function _construct(): void
    {
        $this->_init('digital_cmsbanner', 'banner_id');
    }
}
