<?php
namespace Digital\Testimonials\Model;

class Testimonials extends \Magento\Framework\Model\AbstractModel
{
    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(\Digital\Testimonials\Model\ResourceModel\Testimonials::class);
    }
}
