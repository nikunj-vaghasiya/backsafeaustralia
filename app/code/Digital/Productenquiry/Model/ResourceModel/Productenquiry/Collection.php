<?php
namespace Digital\Productenquiry\Model\ResourceModel\Productenquiry;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    protected $_idFieldName = 'enquiry_id';
    protected function _construct()
    {
        $this->_init(\Digital\Productenquiry\Model\Productenquiry::class,
         \Digital\Productenquiry\Model\ResourceModel\Productenquiry::class);
    }
}
