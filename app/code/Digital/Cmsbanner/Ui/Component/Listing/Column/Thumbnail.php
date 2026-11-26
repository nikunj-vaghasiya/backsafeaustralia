<?php

declare(strict_types=1);

namespace Digital\Cmsbanner\Ui\Component\Listing\Column;

use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\UrlInterface;
use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Ui\Component\Listing\Columns\Column;

class Thumbnail extends Column
{
    public const ALT_FIELD = 'name';

    public function __construct(
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        private readonly StoreManagerInterface $storeManager,
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

        $fieldName = $this->getData('name');

        foreach ($dataSource['data']['items'] as &$item) {
            if (empty($item[$fieldName])) {
                $item[$fieldName . '_src']      = '';
                $item[$fieldName . '_alt']      = '';
                $item[$fieldName . '_link']     = '';
                $item[$fieldName . '_orig_src'] = '';
                continue;
            }

            try {
                $baseUrl = $this->storeManager->getStore()->getBaseUrl(UrlInterface::URL_TYPE_MEDIA);
            } catch (LocalizedException $e) {
                $baseUrl = '';
            }

            $imageUrl = $baseUrl . 'digital/cmsbanner/' . $item[$fieldName];

            $item[$fieldName . '_src']      = $imageUrl;
            $item[$fieldName . '_alt']      = $item[self::ALT_FIELD] ?? '';
            $item[$fieldName . '_link']     = $this->context->getUrl(
                'cmsbanner/banner/edit',
                ['banner_id' => $item['banner_id']]
            );
            $item[$fieldName . '_orig_src'] = $imageUrl;
        }

        return $dataSource;
    }
}
