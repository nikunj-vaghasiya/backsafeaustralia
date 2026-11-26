<?php

namespace Digital\Productenquiry\Controller\Index;

class Add extends \Magento\Framework\App\Action\Action
{
    protected $_resultJsonFactory;

    protected $_dataObjectFactory;

    protected $_jsonHelper;

    protected $_checkoutSession;

    protected $_urlBuilder;

    protected $productModel;

    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Framework\Json\Helper\Data $jsonHelper,
        \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory,
        \Magento\Framework\DataObjectFactory $dataObjectFactory,
        \Magento\Checkout\Model\Session $checkoutSession,
        \Magento\Framework\UrlInterface $urlBuilder,
        \Magento\Catalog\Model\Product $productModel
    ) {

        parent::__construct($context);
        $this->_dataObjectFactory = $dataObjectFactory;
        $this->_jsonHelper = $jsonHelper;
        $this->_resultJsonFactory = $resultJsonFactory;
        $this->_checkoutSession = $checkoutSession;
        $this->_urlBuilder = $urlBuilder;
        $this->productModel = $productModel;

    }

    public function execute()
    {
        $products = $this->getRequest()->getParam('products');
        $productsArray = explode(",", (string)$products);

        if (count($productsArray)) {
            if ($this->_checkoutSession->getEnquiryQuote()) {
                $newQuoteProductsArray = $this->_checkoutSession->getEnquiryQuote();
            } else {
                $newQuoteProductsArray = [];
            }
            foreach ($productsArray as $product) {
                $productIdandQty = explode("=>", (string)$product);
                if (array_key_exists($productIdandQty[0] . $productIdandQty[1], $newQuoteProductsArray)) {
                    $getData = $newQuoteProductsArray[$productIdandQty[0] . $productIdandQty[1]];
                    $getData['qty'] += $productIdandQty[2];
                    $newQuoteProductsArray[$productIdandQty[0] . $productIdandQty[1]] = $getData;
                } else {
                    $product_id = $productIdandQty[0];
                    $product_name = '';
                    $product_sku = '';
                    $product_parentId = '';
                    $product_urlKey = '';
                    if ($product_id > 0) {
                        $product = $this->productModel->load($product_id);
                        $product_name = $product->getName();
                        $product_sku = $product->getSku();
                        $product_parentId = $product->getParentid();
                        $product_urlKey = $product->getParenturl();
                    } else {
                        $product_name = $productIdandQty[3];
                        $product_sku = $productIdandQty[5];
                        $product_parentId = $productIdandQty[1];
                        $product_urlKey = $productIdandQty[4];
                    }

                    $newQuoteProductsArray[$productIdandQty[0] . $productIdandQty[1]] =
                        [
                            'product_id' => $productIdandQty[0],
                            'parentid' => $productIdandQty[1],
                            'qty' => $productIdandQty[2],
                            'name' => $product_name,
                            'parenturl' => $productIdandQty[4],
                            'sku' => $product_sku
                        ];
                }
            }
            if (count($newQuoteProductsArray)) {
                $this->_checkoutSession->setEnquiryQuote($newQuoteProductsArray);
            }
            $result = $this->_urlBuilder->getUrl() . 'productenquiry/index/index';
        } else {
            $result = "error";
        }

        $resultJson = $this->_resultJsonFactory->create();
        return $resultJson->setData($result);
    }
}
