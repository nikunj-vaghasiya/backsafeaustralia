<?php
namespace Digital\Ourteam\Model\Ourteam\Source;

class Status implements \Magento\Framework\Data\OptionSourceInterface
{
    protected $ourteam;
    
    public function __construct(
        \Digital\Ourteam\Model\Ourteam $ourteam
    ) {
        $this->ourteam = $ourteam;
    }

    /**
     * Get options
     *
     * @return array
     */
    public function toOptionArray()
    {
        $options[] = ['label' => '', 'value' => ''];
        $availableOptions = $this->getOptionArray();
        foreach ($availableOptions as $key => $value) {
            $options[] = [
                'label' => $value,
                'value' => $key,
            ];
        }
        return $options;
    }
    
    public static function getOptionArray()
    {
        return [1 => __('Enabled'), 0 => __('Disabled')];
    }
}