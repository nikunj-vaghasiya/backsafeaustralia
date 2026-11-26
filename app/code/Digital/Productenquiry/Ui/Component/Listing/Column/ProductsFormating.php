<?php
namespace Digital\Productenquiry\Ui\Component\Listing\Column;

use Magento\Ui\Component\Listing\Columns\Column;
use Magento\Framework\Serialize\SerializerInterface;

class ProductsFormating extends Column
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
            foreach ($dataSource['data']['items'] as & $item) {
                    
                    $value = $item['products'];
                    $individualHtml = "";
                if ($value!='') {
                    $productsData = @unserialize($value);
                    /*$productsData = $this->serializer->unserialize($value);*/
            
                    if ($productsData !== false || $value === 'b:0;') {
                        foreach ($productsData as $productdata) {
                            if (isset($productdata['sku'])) {
                                $individualHtml .= "<p>".$productdata['name']." [".$productdata['sku']."] "." (".$productdata['qty'].") </p>";
                            } else {
                                $individualHtml .= "<p>".$productdata['name']." (".$productdata['qty'].") </p>";
                            }
                        }
                    }
                    $item['products'] = $individualHtml;
                }
                    
            }
        }
        return $dataSource;
    }
}
