<?php

namespace Integration\Base\Model\Config\Source;

class FilterOptions implements \Magento\Framework\Option\ArrayInterface
{
    const CURRENT_PREVIOUS_DATE = 'current-previous';
    const SPECIFIC_DATE = 'specific-date';
    const NO_FILTER = 'no';

    /**
     * {@inheritdoc}
     *
     * @codeCoverageIgnore
     */
    public function toOptionArray()
    {
        return [
            ['value' => self::CURRENT_PREVIOUS_DATE, 'label' => __('Current/Previous Date')],
            ['value' => self::SPECIFIC_DATE, 'label' => __('Filter With Specific Date')],
            ['value' => self::NO_FILTER, 'label' => __('No')]
        ];
    }
}
