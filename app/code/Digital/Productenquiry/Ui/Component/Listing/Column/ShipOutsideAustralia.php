<?php

namespace Digital\Productenquiry\Ui\Component\Listing\Column;

use Magento\Ui\Component\Listing\Columns\Column;
use Magento\Framework\Serialize\SerializerInterface;

class ShipOutsideAustralia extends Column
{
    /**
     * @var SerializerInterface
     */
    protected $serializer;

    public function __construct(
        \Magento\Framework\View\Element\UiComponent\ContextInterface $context,
        \Magento\Framework\View\Element\UiComponentFactory $uiComponentFactory,
        SerializerInterface $serializer,
        array $components = [],
        array $data = []
    ) {
        parent::__construct($context, $uiComponentFactory, $components, $data);
        $this->serializer = $serializer;
    }
    public function prepareDataSource(array $dataSource)
    {

        if (isset($dataSource['data']['items'])) {
            foreach ($dataSource['data']['items'] as &$item) {

                $value = $item['ship_outside_australia'];
                if ($value == '1') {
                    $item['ship_outside_australia'] = 'Yes';
                }else{
                    $item['ship_outside_australia'] = 'No';
                }
            }
        }
        return $dataSource;
    }
}
