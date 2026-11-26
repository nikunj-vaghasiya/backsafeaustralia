<?php

namespace Digital\Projects\Model;

use Magento\Framework\DataObject\IdentityInterface;

class Products extends \Magento\Framework\Model\AbstractModel
{
    protected function _construct()
    {
        $this->_init(\Digital\Projects\Model\ResourceModel\Products::class);
    }
}
