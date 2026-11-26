<?php



namespace Digital\Productenquiry\Model\ResourceModel;

class Productenquiry extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{

    protected function _construct()
    {

        $this->_init('digital_product_enquiry', 'enquiry_id');
    }
}
