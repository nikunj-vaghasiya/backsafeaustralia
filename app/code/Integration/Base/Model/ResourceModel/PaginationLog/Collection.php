<?php

namespace Integration\Base\Model\ResourceModel\PaginationLog;

class Collection extends \Integration\Base\Model\ResourceModel\AbstractResource\AbstractCollection
{
	/**
     * Resource initialization
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Integration\Base\Model\PaginationLog', 'Integration\Base\Model\ResourceModel\PaginationLog');
    }
}