<?php
namespace Hubspot\ProductEnquiry\Ui\Component\Listing\Column;

use Magento\Framework\UrlInterface;
use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Ui\Component\Listing\Columns\Column;

class ProductLink extends Column
{
    /** @var UrlInterface */
    protected $urlBuilder;

    public function __construct(
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        UrlInterface $urlBuilder,
        array $components = [],
        array $data = []
    ) {
        $this->urlBuilder = $urlBuilder;
        parent::__construct($context, $uiComponentFactory, $components, $data);
    }

    public function prepareDataSource(array $dataSource)
    {
        if (!isset($dataSource['data']['items'])) {
            return $dataSource;
        }

        $fieldName = $this->getData('name'); // product_name
        foreach ($dataSource['data']['items'] as &$item) {
            $name = $item[$fieldName] ?? '';
            if ($name === '' || $name === null) {
                continue;
            }
            $escapedName = htmlspecialchars((string) $name, ENT_QUOTES, 'UTF-8');
            if (!empty($item['product_id'])) {
                $url = $this->urlBuilder->getUrl('catalog/product/edit', ['id' => $item['product_id']]);
                $item[$fieldName] = '<a href="' . $url . '" target="_blank">' . $escapedName . '</a>';
            } else {
                $item[$fieldName] = $escapedName;
            }
        }
        return $dataSource;
    }
}