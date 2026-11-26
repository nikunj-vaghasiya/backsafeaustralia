<?php

namespace Digital\Projects\Model\ResourceModel;

/**
 * ProductsGrid mysql resource
 */
class Products extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('digital_projects_products', 'product_id');
    }
}
