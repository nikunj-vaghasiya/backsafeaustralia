<?php

namespace Integration\Base\Model\Config\Source;

class OrderFilterOptions implements \Magento\Framework\Option\ArrayInterface
{
    const LAST_ONE_YEAR = 'last-one-year';
    const SPECIFIC_DATE = 'specific-date';

    /**
     * {@inheritdoc}
     *
     * @codeCoverageIgnore
     */
    public function toOptionArray()
    {
        return [
            ['value' => self::LAST_ONE_YEAR, 'label' => __('Last One Year')],
            ['value' => self::SPECIFIC_DATE, 'label' => __('Filter With Specific Date')],
        ];
    }
}
