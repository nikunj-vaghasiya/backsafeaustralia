<?php

namespace Digital\Testimonials\Model\ResourceModel\Testimonials;

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
            \Digital\Testimonials\Model\Testimonials::class,
            \Digital\Testimonials\Model\ResourceModel\Testimonials::class
        );
        $this->_map['fields']['page_id'] = 'main_table.page_id';
    }
}
