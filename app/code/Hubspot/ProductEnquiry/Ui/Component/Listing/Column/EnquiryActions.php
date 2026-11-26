<?php
namespace Hubspot\ProductEnquiry\Ui\Component\Listing\Column;

use Magento\Framework\UrlInterface;
use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Ui\Component\Listing\Columns\Column;

class EnquiryActions extends Column
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
        if (isset($dataSource['data']['items'])) {
            foreach ($dataSource['data']['items'] as &$item) {
                if (!isset($item['entity_id'])) {
                    continue;
                }
                $item[$this->getData('name')] = [
                    'view' => [
                        'href' => $this->urlBuilder->getUrl(
                            'productenquiry/enquiry/view',
                            ['entity_id' => $item['entity_id']]
                        ),
                        'label' => __('View'),
                    ],
                    'delete' => [
                        'href' => $this->urlBuilder->getUrl(
                            'productenquiry/enquiry/delete',
                            ['entity_id' => $item['entity_id']]
                        ),
                        'label' => __('Delete'),
                        'confirm' => [
                            'title' => __('Delete enquiry'),
                            'message' => __('Are you sure you want to delete this enquiry?'),
                        ],
                    ],
                ];
            }
        }
        return $dataSource;
    }
}
