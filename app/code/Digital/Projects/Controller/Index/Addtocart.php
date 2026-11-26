<?php

namespace Digital\Projects\Controller\Index;

use Magento\Framework\App\Action\Action;
use Magento\Framework\View\Result\PageFactory;
use Magento\Framework\App\Action\Context;
use Magento\Framework\View\Result\Page; 
 
class Addtocart extends Action
{
    protected $resultPageFactory;
    protected $formKey;   
    protected $cart;
    protected $product;

    public function __construct(
        Context $context,
        PageFactory $resultPageFactory,
        \Magento\Framework\Data\Form\FormKey $formKey,
        \Magento\Checkout\Model\Cart $cart,
        \Magento\Catalog\Model\Product $product
    ){
        parent::__construct($context);
        $this->resultPageFactory = $resultPageFactory;
        $this->formKey = $formKey;
        $this->cart = $cart;
        $this->product = $product;
    }

    public function execute()
    {
        try {       
        $params = $this->getRequest()->getParams();

        $all_products = $params['projects_related_products'];        
        $_product = '';
        foreach ($all_products as $key => $value) {                        
            $_product = '';
            if($value>0)
            {

                $_product = '';
                $productId = $value;
                $qty = 1;

                $params = array(                        
                        'product' => $productId,
                        'qty'   =>$qty
                    );
                
                                
                $objectManager =  \Magento\Framework\App\ObjectManager::getInstance();
                $_product = $objectManager->create('\Magento\Catalog\Model\Product')->load($productId); 
                
				if($_product)
				{
					
					$this->cart->addProduct($_product, $params);
	
					$message = __(
							'You added %1 to your shopping cart.',
							$_product->getName()
						);
						
					$productNames[] = '"' . $_product->getName() . '"';
					
				}
                else
                {
                $this->cart->addProduct($_product, $params);

                $message = __(
                        'You added %1 to your shopping cart.',
                        $_product->getName()
                    );
                    
                $productNames[] = '"' . $_product->getName() . '"';
                }
                
            }
            
        }
        $message = __(
                    'You added %1 to your shopping cart.', join(', ', $productNames)
                    );
        $this->messageManager->addSuccessMessage($message);
        $this->cart->save();
        $cartUrl = $this->_objectManager->get(\Magento\Checkout\Helper\Cart::class)->getCartUrl();
        $this->_redirect($cartUrl);
        return;
    }
    catch (\Exception $e) {
            $this->messageManager->addException($e, __('We can\'t add this item to your shopping cart right now.'));            
            $this->_redirect('*/');
            return;
        }
        
        
    }   
}
