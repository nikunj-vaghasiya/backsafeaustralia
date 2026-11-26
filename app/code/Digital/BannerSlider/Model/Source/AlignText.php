<?php

namespace Digital\BannerSlider\Model\Source;

use Magento\Eav\Model\Entity\Attribute\Source\AbstractSource;
use Magento\Eav\Model\Entity\Attribute\Source\SourceInterface;
use Magento\Framework\Data\OptionSourceInterface;

class AlignText extends AbstractSource implements SourceInterface, OptionSourceInterface
{
    /* @codingStandardsIgnoreStart */
    const CENTER_ALIGN_TEXT = 'center';

    const LEFT_ALIGN_TEXT = 'left';

    const RIGHT_ALIGN_TEXT = 'right';

    /**
     * Retrieve option array
     *
     * @return string[]
     */
    public static function getOptionArray()
    {
        return [
            self::CENTER_ALIGN_TEXT => __('Center'),
            self::LEFT_ALIGN_TEXT => __('Left'),
            self::RIGHT_ALIGN_TEXT => __('Right'),
        ];
    }
    /* @codingStandardsIgnoreEnd */

    /**
     * Retrieve option array with empty value
     *
     * @return string[]
     */
    public function getAllOptions()
    {
        $result = [];

        foreach (self::getOptionArray() as $index => $value) {
            $result[] = ['value' => $index, 'label' => $value];
        }

        return $result;
    }

    /**
     * Retrieve option text by option value
     *
     * @param string $optionId
     * @return string
     */
    public function getOptionText($optionId)
    {
        $options = self::getOptionArray();

        return isset($options[$optionId]) ? $options[$optionId] : null;
    }
}
