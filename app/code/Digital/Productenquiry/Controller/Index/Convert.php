<?php
namespace Digital\Productenquiry\Controller\Index;

use Magento\Framework\Controller\ResultFactory;

class Convert extends \Magento\Framework\App\Action\Action
{

    protected $_dataObjectFactory;
    protected $_checkoutSession;
    protected $_urlBuilder;
    protected $cart;
    private $productFactory;

    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Framework\Controller\ResultFactory $resultFactory,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Checkout\Model\Session $checkoutSession,
        \Magento\Checkout\Model\Cart $cart,
        \Magento\Catalog\Model\ProductFactory $productFactory
    ) {
        parent::__construct($context);
        $this->resultFactory = $resultFactory;
        $this->_storeManager = $storeManager;
        $this->_checkoutSession = $checkoutSession;
        $this->cart = $cart;
        $this->productFactory = $productFactory;
    }
    public function execute()
    {
        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
        try {
            $items = $this->cart->getQuote()->getAllVisibleItems();
            if (count($items)>0) {
                foreach ($items as $item) {
                    $product = $this->productFactory->create();
                    $product->load($item->getProductId());
                    
                    $prod_id = $item['product_id'];
                    $parentid = $item['product_id'];
                    $prod_qty = $item['qty'];
                    $name = $item['name'];
                    $parenturl = $product->getProductUrl();
                    $sku = $item['sku'];
                    $type = $item['product_type'];
                
                    if ($prod_id!='' && $parentid!='') {
                        if ($this->_checkoutSession->getEnquiryQuote()) {
                            $newQuoteProductsArray = $this->_checkoutSession->getEnquiryQuote();
                        } else {
                            $newQuoteProductsArray = [];
                        }
                        if (array_key_exists($prod_id.$parentid, $newQuoteProductsArray)) {
                            $getData = $newQuoteProductsArray[$prod_id.$parentid];
                            $getData['qty'] += $prod_qty;
                            $newQuoteProductsArray[$prod_id.$parentid] = $getData;
                        } else {
                            $newQuoteProductsArray[$prod_id.$parentid] =
                                [
                                    'product_id' => $prod_id,
                                    'parentid' =>$parentid,
                                    'qty'=> $prod_qty,
                                    'name'=> $name,
                                    'parenturl'=> $parenturl,
                                    'sku'=> $sku,
                                    'type'=> $type
        
                                 ];
                        }
                        if (count($newQuoteProductsArray)) {
                            $this->_checkoutSession->setEnquiryQuote($newQuoteProductsArray);
                        }
        
                    }
                }
                $this->messageManager->addSuccess(__('Your cart items has been copied into quote items.'));
                // redirect to quote page
                $resultRedirect->setUrl($this->_storeManager->getStore()->getBaseUrl().'productenquiry');
                
            }
        
        } catch (\Exception $e) {
            $this->messageManager->addError(__('We can\'t add item to your quote right now.'));
            $resultRedirect->setUrl($this->_redirect->getRefererUrl());
        }
        return $resultRedirect;
    }
}
