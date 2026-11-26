<?php
namespace Digital\Productenquiry\Block\Adminhtml\Productenquiry\Renderer;

use Magento\Framework\DataObject;

class Yesno extends \Magento\Framework\Data\Form\Element\AbstractElement
{
    public function getElementHtml()
    {
        $value = $this->getValue();
        $individualHtml = "";
        if ($value=='1') {
            $individualHtml = 'Yes';
        } else {
            $individualHtml = 'No';
        }
        return $individualHtml;
    }
}
