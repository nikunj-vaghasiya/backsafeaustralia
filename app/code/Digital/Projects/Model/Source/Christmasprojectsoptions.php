<?php

namespace Digital\Projects\Model\Source;

use Magento\Framework\Data\OptionSourceInterface;


class Christmasprojectsoptions implements OptionSourceInterface
{
   
  public function toOptionArray(){
    
         return [
            ['label' => __('-- Please Select --'), 'value' => ''],
            ['label' => __('Christmas Projects'), 'value' => '1'],
        ];
    }
}