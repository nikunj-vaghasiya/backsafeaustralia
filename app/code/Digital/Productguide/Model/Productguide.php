<?php

namespace Digital\Productguide\Model;

/**
 * Productguide Model
 *
 * @method \Digital\Productguide\Model\Resource\Page _getResource()
 * @method \Digital\Productguide\Model\Resource\Page getResource()
 */
class Productguide extends \Magento\Framework\Model\AbstractModel
{
    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(\Digital\Productguide\Model\ResourceModel\Productguide::class);
    }
}
