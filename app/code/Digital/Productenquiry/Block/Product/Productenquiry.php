<?php

namespace Digital\Productenquiry\Block\Product;

use Magento\Catalog\Helper\Image;
use Magento\Catalog\Model\ProductFactory;

class Productenquiry extends \Magento\Framework\View\Element\Template
{
    protected $_checkoutSession;
    private $redirectFactory;
    private $moduleManager;
    protected $customerSession;
    protected $imageHelper;
    protected $productFactory;

    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Framework\ObjectManagerInterface $objectManager,
        \Magento\Framework\Registry $registry,
        \Magento\Checkout\Model\Session $checkoutSession,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Framework\Controller\Result\RedirectFactory $redirectFactory,
        \Magento\Framework\Module\Manager $moduleManager,
        \Magento\Customer\Api\AddressRepositoryInterface $addressRepository,
        \Magento\Customer\Api\CustomerRepositoryInterface $customerRepository,
        Image $imageHelper,
        ProductFactory $productFactory,
        array $data = []
    ) {
        $this->_coreRegistry = $registry;
        $this->objectManager = $objectManager;
        $this->_checkoutSession = $checkoutSession;
        $this->redirectFactory = $redirectFactory;
        $this->_moduleManager = $moduleManager;
        $this->customerRepository = $customerRepository;
        $this->addressRepository = $addressRepository;
        $this->customerSession = $customerSession;
        $this->imageHelper = $imageHelper;
        $this->productFactory = $productFactory;
        parent::__construct($context, $data);
    }

    public function isModuleEnabled()
    {
        return $this->_moduleManager->isEnabled('Digital_Productenquiry');
    }

    public function isExistEnquiryQuote()
    {
        if ($this->_checkoutSession->getEnquiryQuote()) {
            return 1;
        }
    }

    public function getFirstname()
    {
        $first_name = '';
        $customerId = $this->customerSession->getCustomer()->getId();
        if ($customerId) {
            $customer = $this->customerRepository->getById($customerId);
            $first_name = $customer->getFirstName();
        }
        return $first_name;
    }

    public function getLastname()
    {
        $last_name = '';
        $customerId = $this->customerSession->getCustomer()->getId();
        if ($customerId) {
            $customer = $this->customerRepository->getById($customerId);
            $last_name = $customer->getLastName();
        }
        return $last_name;
    }

    public function getEmailaddress()
    {
        return trim((string) $this->customerSession->getCustomer()->getEmail());
    }

    public function getCompayName()
    {
        $customer = $this->customerSession->getCustomer();
        $shippingAddress = $customer->getDefaultShippingAddress();
        if ($shippingAddress) {
            return $shippingAddress->getCompany();
        }
    }

    public function getEnquiryQuoteProducts()
    {
        if ($this->_checkoutSession->getEnquiryQuote()) {
            return $this->_checkoutSession->getEnquiryQuote();
        } else {
            return $this->redirectFactory->create()->setPath('*/*/');
        }
    }

    public function getProductImage($product_id,$parentid)
    {
        try {
            $product = $this->productFactory->create()->load($product_id);
        } catch (NoSuchEntityException $e) {
            return 'Data not found';
        }
        $productSmall_image =  $product->getData('small_image');
        $product_image = '';
        if ($productSmall_image == null || $productSmall_image == 'no_selection') {
            $parentProduct = $product = $this->productFactory->create()->load($parentid);
            $product_image = $this->imageHelper->init($parentProduct, 'small_image', ['type' => 'small_image'])->keepAspectRatio(true)->resize('90', '90')->getUrl();
        } else {
            $product_image = $this->imageHelper->init($product, 'small_image', ['type' => 'small_image'])->keepAspectRatio(true)->resize('90', '90')->getUrl();
        }
        return $product_image;
    }
}
