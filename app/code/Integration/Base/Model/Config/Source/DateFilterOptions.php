<?php

namespace Integration\Base\Model\Config\Source;

class DateFilterOptions implements \Magento\Framework\Option\ArrayInterface
{   
    const PREVIOUS_DATE = 'previous-date';
    const CURRENT_DATE = 'current-date';

    /**
     * {@inheritdoc}
     *
     * @codeCoverageIgnore
     */
    public function toOptionArray()
    {
        return [
            ['value' => self::PREVIOUS_DATE, 'label' => __('Previous Date')],
            ['value' => self::CURRENT_DATE, 'label' => __('Current Date')]
        ];
    }
}
