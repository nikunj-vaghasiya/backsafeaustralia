<?php

declare(strict_types=1);

namespace Digital\Cmsbanner\Block\Adminhtml\Banner\Edit;

use Magento\Backend\Block\Widget\Context;
use Magento\Framework\View\Element\UiComponent\Control\ButtonProviderInterface;

class DeleteButton implements ButtonProviderInterface
{
    public function __construct(
        private readonly Context $context
    ) {}

    public function getButtonData(): array
    {
        $bannerId = $this->context->getRequest()->getParam('banner_id');

        if (!$bannerId) {
            return [];
        }

        return [
            'label'      => __('Delete'),
            'class'      => 'delete',
            'on_click'   => sprintf(
                "deleteConfirm('%s', '%s', {data: {}})",
                __('Are you sure you want to delete this banner?'),
                $this->context->getUrlBuilder()->getUrl(
                    '*/*/delete',
                    ['banner_id' => $bannerId]
                )
            ),
            'sort_order' => 20,
        ];
    }
}
