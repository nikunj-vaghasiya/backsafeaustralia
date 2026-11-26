<?php

declare(strict_types=1);

namespace Digital\Cmsbanner\Model\Banner\Source;

use Digital\Cmsbanner\Api\Data\BannerInterface;
use Magento\Framework\Data\OptionSourceInterface;

class Status implements OptionSourceInterface
{
    public function toOptionArray(): array
    {
        return [
            ['value' => BannerInterface::STATUS_ENABLED,  'label' => __('Enabled')],
            ['value' => BannerInterface::STATUS_DISABLED, 'label' => __('Disabled')],
        ];
    }
}
