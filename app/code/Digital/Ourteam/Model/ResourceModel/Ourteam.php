<?php

namespace Digital\Ourteam\Model\ResourceModel;

/**
 * Ourteam Resource Model
 */
class Ourteam extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('digital_ourteam', 'team_id');
    }
}
