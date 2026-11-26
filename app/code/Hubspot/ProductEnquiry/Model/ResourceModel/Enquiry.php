<?php
namespace Hubspot\ProductEnquiry\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

class Enquiry extends AbstractDb
{
    protected function _construct()
    {
        $this->_init('hubspot_product_enquiry', 'entity_id');
    }
}
