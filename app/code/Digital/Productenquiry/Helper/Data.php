<?php
namespace Digital\Productenquiry\Helper;

use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Store\Model\ScopeInterface;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\App\Request\DataPersistorInterface;

class Data extends \Magento\Framework\App\Helper\AbstractHelper
{
    protected $scopeConfig;
    protected $httpFactory;
    protected $storeManager;
    protected $customerSession;
    protected $inlineTranslation;
    protected $formKey;
    private $dataPersistor;
    protected $checkoutSession;
    private $postData = null;

    public const XML_PATH_ENABLE = "productenquiry/view/enabled";
    public const XML_PATH_RECIPIENT_EMAIL = "productenquiry/view/recipient_email";
    public const XML_PATH_GENERAL_NAME = 'trans_email/ident_general/name';
    public const XML_PATH_GENERAL_EMAIL = 'trans_email/ident_general/email';
    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Framework\Data\Form\FormKey $formKey,
        \Magento\Framework\HTTP\Adapter\FileTransferFactory $httpFactory,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Checkout\Model\Session $checkoutSession,
        \Magento\Framework\Translate\Inline\StateInterface $inlineTranslation
    ) {
        $this->scopeConfig = $scopeConfig;
        $this->formKey = $formKey;
        $this->httpFactory = $httpFactory;
        $this->storeManager = $storeManager;
        $this->customerSession = $customerSession;
        $this->checkoutSession = $checkoutSession;
        $this->inlineTranslation = $inlineTranslation;
        parent::__construct($context);
    }
    public function getCountTotalEnquiryQty()
    {
        $qty = 0;
        if ($this->checkoutSession->getEnquiryQuote()) {
            $newQuoteProductsArray = $this->checkoutSession->getEnquiryQuote();
            if (count($newQuoteProductsArray) > 0) {
                foreach ($newQuoteProductsArray as $key => $val) {
                    $item = $val;
                    $qty+= $item['qty'];
                }
                return $qty;
            } else {
                return $qty;
            }
        } else {
            return $qty;
        }
    }
    public function getConfig($config_path)
    {
        return $this->scopeConfig->getValue($config_path, \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
    }
    public function Recipientemail()
    {
        $storeScope = \Magento\Store\Model\ScopeInterface::SCOPE_STORE;
        return $this->scopeConfig->getValue(self::XML_PATH_RECIPIENT_EMAIL, $storeScope);
    }
    public function GeneralName()
    {
        $storeScope = \Magento\Store\Model\ScopeInterface::SCOPE_STORE;
        return $this->scopeConfig->getValue(self::XML_PATH_GENERAL_NAME, $storeScope);
    }
    public function GeneralEmail()
    {
        $storeScope = \Magento\Store\Model\ScopeInterface::SCOPE_STORE;
        return $this->scopeConfig->getValue(self::XML_PATH_GENERAL_EMAIL, $storeScope);
    }
    public function isCustomerLoggedIn()
    {
        return $this->customerSession;
    }
    public function getPostValue($key)
    {
        if (null === $this->postData) {
            $this->postData = (array) $this->getDataPersistor()->get('productenquiry-form');
            $this->getDataPersistor()->clear('productenquiry-form');
        }

        if (isset($this->postData[$key])) {
            return (string) $this->postData[$key];
        }

        return '';
    }
    private function getDataPersistor()
    {
        if ($this->dataPersistor === null) {
            $this->dataPersistor = ObjectManager::getInstance()
                ->get(DataPersistorInterface::class);
        }

        return $this->dataPersistor;
    }
}
