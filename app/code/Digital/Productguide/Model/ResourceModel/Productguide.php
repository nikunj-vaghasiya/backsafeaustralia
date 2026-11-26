<?php

namespace Digital\Productguide\Model\ResourceModel;

/**
 * Productguide Resource Model
 */
class Productguide extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('digital_productguide', 'productguide_id');
    }
}
