<?php

namespace Integration\Product\Model\ResourceModel\Product;

class Collection extends \Integration\Base\Model\ResourceModel\AbstractResource\AbstractCollection
{
	/**
     * Resource initialization
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Integration\Product\Model\Product', 'Integration\Product\Model\ResourceModel\Product');
    }
}