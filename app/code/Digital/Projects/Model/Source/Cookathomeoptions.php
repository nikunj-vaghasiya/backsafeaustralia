<?php

namespace Digital\Projects\Model\Source;

use Magento\Framework\Data\OptionSourceInterface;


class Cookathomeoptions implements OptionSourceInterface
{
   
  public function toOptionArray(){
    
         return [
            ['label' => __('-- Please Select --'), 'value' => ''],
            ['label' => __('Cook At Home'), 'value' => '1'],
        ];
    }
}