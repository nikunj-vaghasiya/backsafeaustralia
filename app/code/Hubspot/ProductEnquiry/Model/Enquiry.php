<?php
namespace Hubspot\ProductEnquiry\Model;

use Magento\Framework\Model\AbstractModel;

class Enquiry extends AbstractModel
{
    protected function _construct()
    {
        $this->_init(\Hubspot\ProductEnquiry\Model\ResourceModel\Enquiry::class);
    }
}
