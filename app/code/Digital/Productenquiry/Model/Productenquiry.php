<?php



namespace Digital\Productenquiry\Model;

class Productenquiry extends \Magento\Framework\Model\AbstractModel
{

    protected function _construct()
    {

        $this->_init(\Digital\Productenquiry\Model\ResourceModel\Productenquiry::class);
    }
}
