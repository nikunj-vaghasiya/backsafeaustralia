<?php

namespace Digital\Productenquiry\Controller\Index;

class Group extends \Magento\Framework\App\Action\Action
{
    protected $_resultJsonFactory;
    
    protected $_dataObjectFactory;
    
    protected $_jsonHelper;
    
    protected $_checkoutSession;
    
    protected $_urlBuilder;
    protected $resultRedirectFactory;
    
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Framework\Json\Helper\Data $jsonHelper,
        \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory,
        \Magento\Framework\DataObjectFactory $dataObjectFactory,
        \Magento\Checkout\Model\Session $checkoutSession,
        \Magento\Framework\UrlInterface $urlBuilder,
        \Magento\Framework\Controller\Result\RedirectFactory $resultRedirectFactory
    ) {
       
        parent::__construct($context);
        $this->_dataObjectFactory = $dataObjectFactory;
        $this->_jsonHelper = $jsonHelper;
         $this->resultRedirectFactory = $resultRedirectFactory;
        $this->_resultJsonFactory = $resultJsonFactory;
        $this->_checkoutSession = $checkoutSession;
        $this->_urlBuilder = $urlBuilder;
    }
    
    public function execute()
    {
        $products = $this->getRequest()->getParam('products');
        $name = $this->getRequest()->getParam('name');
        
        if (count($products)) {
            if ($this->_checkoutSession->getEnquiryQuote()) {
                $newQuoteProductsArray = $this->_checkoutSession->getEnquiryQuote();
            } else {
                $newQuoteProductsArray = [];
            }
            foreach ($products as $product) {
                if (array_key_exists($product['id'].$product['parentid'], $newQuoteProductsArray)) {
                        $getData = $newQuoteProductsArray[$product['id'].$product['parentid']];
                        $getData['qty'] += $product['prod_qty'];
                        $newQuoteProductsArray[$product['id'].$product['parentid']] = $getData;
                } else {
                    $newQuoteProductsArray[$product['id'].$product['parentid']] =
                        [
                                'product_id' => $product['id'],
                                'parentid' =>$product['parentid'],
                                'qty'=> $product['prod_qty'],
                                'name'=> $product['name'],
                                'parenturl'=> $product['parenturl'],
                                'sku'=> $product['sku'],
                                'type'=> 'grouped'
                            ];
                }
            }
            
            if (count($newQuoteProductsArray)) {
                $this->_checkoutSession->setEnquiryQuote($newQuoteProductsArray);
            }
            $message = __(
                'You added %1 to your Quote.',
                $name
            );
                $result = [
                    'status' => 'success',
                    'message' => $message,
                    'count'=>count($newQuoteProductsArray)
                ];
                $this->messageManager->addSuccessMessage($message);
                /*$this->messageManager->addSuccessMessage($message);
                $resultRedirect = $this->resultRedirectFactory->create();
                $resultRedirect->setPath('productenquiry/index/index');
                return $resultRedirect;*/

            //$result = $this->_urlBuilder->getUrl().'productenquiry/index/index';
        } else {
            $result = [
                    'status' => 'error',
                    'message' => 'error',
                ];
                $this->messageManager->addErrorMessage(
                    $e,
                    __('We can\'t add this item to your Quote right now.')
                );
        }
        
        $resultJson = $this->_resultJsonFactory->create();
        return $resultJson->setData($result);
    }
}
