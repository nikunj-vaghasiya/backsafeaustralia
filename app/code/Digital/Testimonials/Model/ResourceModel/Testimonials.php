<?php
namespace Digital\Testimonials\Model\ResourceModel;

class Testimonials extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('digital_testimonials', 'testimonial_id');
    }
}
