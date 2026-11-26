<?php

namespace Digital\Projects\Model\Source;

use Magento\Framework\Data\OptionSourceInterface;


class Winterwarmerprojectsoptions implements OptionSourceInterface
{
   
  public function toOptionArray(){
    
         return [
            ['label' => __('-- Please Select --'), 'value' => ''],
            ['label' => __('Winter Warmer Projects'), 'value' => '1'],
        ];
    }
}