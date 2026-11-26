<?php
namespace Digital\Productguide\Model\ResourceModel\Productguide;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    /**
     * Resource initialization
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(
            \Digital\Productguide\Model\Productguide::class,
            \Digital\Productguide\Model\ResourceModel\Productguide::class
        );
    }
}
