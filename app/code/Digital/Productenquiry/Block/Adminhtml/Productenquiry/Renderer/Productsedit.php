<?php
namespace Digital\Productenquiry\Block\Adminhtml\Productenquiry\Renderer;

use Magento\Framework\DataObject;
use Magento\Framework\Serialize\SerializerInterface;

class Productsedit extends \Magento\Framework\Data\Form\Element\AbstractElement
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
    
    public function getElementHtml()
    {
        $value = $this->getValue();
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
