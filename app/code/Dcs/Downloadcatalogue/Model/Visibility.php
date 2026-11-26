<?php
 
namespace Dcs\Downloadcatalogue\Model;

class Visibility implements \Magento\Framework\Option\ArrayInterface
{
    /**
     * Get Grid row status type labels array.
     * @return array
     */
    public const VISIBILITY_DESKTOP = 1;
    public const VISIBILITY_MOBILE = 2;
    public const VISIBILITY_RATINA = 3;
    public function getOptionArray()
    {
        $options = ['1' => __('Desktop'),'2' => __('Mobile'),'3' => __('Ratina')];
        return $options;
    }
    
    /**
     * Get Grid row status labels array with empty value for option element.
     *
     * @return array
     */
    public function getAllOptions()
    {
        $res = $this->getOptions();
        array_unshift($res, ['value' => '', 'label' => '']);
        return $res;
    }
 
    /**
     * Get Grid row type array for option element.
     * @return array
     */
    public function getOptions()
    {
        $res = [];
        foreach ($this->getOptionArray() as $index => $value) {
            $res[] = ['value' => $index, 'label' => $value];
        }
        return $res;
    }
 
    /**
     * {@inheritdoc}
     */
    public function toOptionArray()
    {
        return $this->getOptions();
    }
}
