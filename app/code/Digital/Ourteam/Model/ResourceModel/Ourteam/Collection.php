<?php

/**
 * Ourteam Resource Collection
 */
namespace Digital\Ourteam\Model\ResourceModel\Ourteam;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    /**
     * Primary column
     *
     * @var string
     */
    protected $_idFieldName = 'team_id';

    /**
     * Resource initialization
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(
            \Digital\Ourteam\Model\Ourteam::class,
            \Digital\Ourteam\Model\ResourceModel\Ourteam::class
        );
    }
}
