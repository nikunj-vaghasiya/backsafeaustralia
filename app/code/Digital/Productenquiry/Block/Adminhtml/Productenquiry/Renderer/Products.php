<?php
namespace Digital\Productenquiry\Block\Adminhtml\Productenquiry\Renderer;

use Magento\Framework\DataObject;
use Magento\Framework\Serialize\SerializerInterface;

class Products extends \Magento\Backend\Block\Widget\Grid\Column\Renderer\AbstractRenderer
{
    /**
    * @var SerializerInterface
    */
    protected $serializer;

    /**
    * @param SerializerInterface $serializer
    */
    public function __construct(SerializerInterface $serializer) {
    $this->serializer = $serializer;
    }

    public function render(DataObject $row)
    {
        $value = $row->getData($this->getColumn()->getIndex());
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
        }
        return $individualHtml;
    }
}
