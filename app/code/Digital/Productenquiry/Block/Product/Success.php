<?php

namespace Digital\Productenquiry\Block\Product;

use Magento\Catalog\Helper\Image;
use Magento\Catalog\Model\ProductFactory;

class Success extends \Magento\Framework\View\Element\Template
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
}
