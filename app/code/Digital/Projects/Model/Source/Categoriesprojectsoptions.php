<?php

namespace Digital\Projects\Model\Source;

use Magento\Framework\Data\OptionSourceInterface;

class Categoriesprojectsoptions implements OptionSourceInterface
{
   
  public function toOptionArray(){
    
         return [
            ['label' => __('-- Please Select --'), 'value' => ''],
            ['label' => __('Family Pleasers'), 'value' => '1'],
            ['label' => __('Grill & BBQ'), 'value' => '2'],
            ['label' => __('Quick & Easy'), 'value' => '3'],
            ['label' => __('Slow Cooking / Casserole'), 'value' => '4'],
            ['label' => __('Special Occasions'), 'value' => '5']
        ];
    }
}