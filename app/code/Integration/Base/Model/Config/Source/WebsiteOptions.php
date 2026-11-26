<?php

namespace Integration\Base\Model\Config\Source;

class WebsiteOptions implements \Magento\Framework\Option\ArrayInterface
{
    const SINGLE_WEBSITE = 'single';
    const MULTIPLE_WEBSITE = 'multiple';
    

    /**
     * {@inheritdoc}
     *
     * @codeCoverageIgnore
     */
    public function toOptionArray()
    {
        return [
            ['value' => self::SINGLE_WEBSITE, 'label' => __('Single Website')],
            ['value' => self::MULTIPLE_WEBSITE, 'label' => __('Multiple Website')]            
        ];
    }
}
