<?php

namespace Digital\Projects\Model\Source;

use Magento\Framework\Data\OptionSourceInterface;


class Cookemupprojectsoptions implements OptionSourceInterface
{
   
  public function toOptionArray(){
    
         return [
            ['label' => __('-- Please Select --'), 'value' => ''],
            ['label' => __('Cookemup Camping Projects'), 'value' => '1'],
        ];
    }
}