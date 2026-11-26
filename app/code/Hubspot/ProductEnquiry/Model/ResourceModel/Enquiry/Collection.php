<?php
namespace Hubspot\ProductEnquiry\Model\ResourceModel\Enquiry;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

class Collection extends AbstractCollection
{
    protected $_idFieldName = 'entity_id';

    protected function _construct()
    {
        $this->_init(
            \Hubspot\ProductEnquiry\Model\Enquiry::class,
            \Hubspot\ProductEnquiry\Model\ResourceModel\Enquiry::class
        );
    }
}
