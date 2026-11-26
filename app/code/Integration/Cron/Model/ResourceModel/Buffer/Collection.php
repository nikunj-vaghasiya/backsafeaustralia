<?php

namespace Integration\Cron\Model\ResourceModel\Buffer;

class Collection extends \Integration\Base\Model\ResourceModel\AbstractResource\AbstractCollection
{
	/**
     * Resource initialization
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Integration\Cron\Model\Buffer', 'Integration\Cron\Model\ResourceModel\Buffer');
    }
}