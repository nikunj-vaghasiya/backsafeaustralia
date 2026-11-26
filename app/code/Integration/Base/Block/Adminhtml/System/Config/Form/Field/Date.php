<?php

namespace Integration\Base\Block\Adminhtml\System\Config\Form\Field;

use Magento\Backend\Block\Template\Context;
use Magento\Framework\Stdlib\DateTime;

class Date extends \Magento\Config\Block\System\Config\Form\Field
{
    
    /**
     * @param Context $context    
     * @param array $data
     */
    public function __construct(
        Context $context,        
        array $data = []
    ) {
        parent::__construct($context, $data);
    }

    public function render(\Magento\Framework\Data\Form\Element\AbstractElement $element)
    {
        $element->setDateFormat(DateTime::DATE_INTERNAL_FORMAT);
        $element->setTimeFormat(null);
        $element->setReadonly(false);
        return parent::render($element);
    }
}
