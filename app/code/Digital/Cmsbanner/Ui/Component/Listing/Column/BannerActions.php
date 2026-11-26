<?php

declare(strict_types=1);

namespace Digital\Cmsbanner\Ui\Component\Listing\Column;

use Magento\Framework\UrlInterface;
use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Ui\Component\Listing\Columns\Column;

class BannerActions extends Column
{
    public function __construct(
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        private readonly UrlInterface $urlBuilder,
        array $components = [],
        array $data = []
    ) {
        parent::__construct($context, $uiComponentFactory, $components, $data);
    }

    public function prepareDataSource(array $dataSource): array
    {
        if (!isset($dataSource['data']['items'])) {
            return $dataSource;
        }

        foreach ($dataSource['data']['items'] as &$item) {
            if (!isset($item['banner_id'])) {
                continue;
            }

            $item[$this->getData('name')] = [
                'edit' => [
                    'href'  => $this->urlBuilder->getUrl('cmsbanner/banner/edit', ['banner_id' => $item['banner_id']]),
                    'label' => __('Edit'),
                ],
                'delete' => [
                    'href'    => $this->urlBuilder->getUrl('cmsbanner/banner/delete', ['banner_id' => $item['banner_id']]),
                    'label'   => __('Delete'),
                    'confirm' => [
                        'title'   => __('Delete Banner'),
                        'message' => __('Are you sure you want to delete this banner?'),
                    ],
                    'post'    => true,
                ],
            ];
        }

        return $dataSource;
    }
}
