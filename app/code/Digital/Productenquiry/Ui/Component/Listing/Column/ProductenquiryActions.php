<?php
namespace Digital\Productenquiry\Ui\Component\Listing\Column;

use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Ui\Component\Listing\Columns\Column;
use Magento\Framework\UrlInterface;

class ProductenquiryActions extends \Magento\Ui\Component\Listing\Columns\Column
{
    protected $urlBuilder;

    public function __construct(
        ContextInterface $context, 
        UiComponentFactory $uiComponentFactory, 
        UrlInterface $urlBuilder, 
        array $components = [], 
        array $data = [])
    {
        $this->urlBuilder = $urlBuilder;
        parent::__construct($context, $uiComponentFactory, $components, $data);
    }

    public function prepareDataSource(array $dataSource)
    {
        if (isset($dataSource['data']['items'])) {
            foreach ($dataSource['data']['items'] as &$item) {
                $item[$this->getData('name')]['edit'] = [
                    'href' => $this->urlBuilder->getUrl('productenquiry/index/edit', 
                        ['enquiry_id' => $item['enquiry_id']]),
                    'label' => __('View'),
                    'hidden' => false
                ];
            }
        }

        return $dataSource;
    }
}
