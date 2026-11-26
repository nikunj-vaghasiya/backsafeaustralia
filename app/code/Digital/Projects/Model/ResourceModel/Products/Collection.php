<?php

namespace Digital\Projects\Model\ResourceModel\Products;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    /**
     * Define resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(
            \Digital\Projects\Model\Products::class,
            \Digital\Projects\Model\ResourceModel\Products::class
        );
    }
}
