<?php
namespace Digital\Projects\Model\Source;

use Magento\Framework\Data\OptionSourceInterface;

class Proteinprojectsoptions implements OptionSourceInterface
{
   
  public function toOptionArray(){
    
         return [
            ['label' => __('-- Please Select --'), 'value' => ''],
            ['label' => __('Beef'), 'value' => '1'],
            ['label' => __('Lamb'), 'value' => '2'],
            ['label' => __('Chicken'), 'value' => '3'],
            ['label' => __('Pork'), 'value' => '4']
        ];
    }
}