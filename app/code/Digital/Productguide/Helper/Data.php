<?php
namespace Digital\Productguide\Helper;

use Magento\Customer\Helper\View as CustomerViewHelper;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\App\Request\DataPersistorInterface;
use Magento\Store\Model\ScopeInterface;

class Data extends \Magento\Framework\App\Helper\AbstractHelper
{
    /**
     * Check Customer session
     *
     * @var \Magento\Customer\Model\Session
     */
    protected $_customerSession;

    /**
     * Path to store config where count of Productguide posts per page is stored
     *
     * @var string
     */
    private $postData = null;

    /**
     * @var DataPersistorInterface
     */
    private $dataPersistor;

    /**
     * @var \Magento\Customer\Helper\View
     */
    protected $_customerViewHelper;

    protected $_scopeConfig;
    protected $_storeInterface;

    /*@codingStandardsIgnoreStart*/
    const XML_PATH_ENABLE = "productguide/view/enabled";
    const XML_PATH_SITEKEY = "digitalconfig/captchakey/recapcha_sitekey";
    const XML_PATH_SECRETKEY = "digitalconfig/captchakey/recapcha_secretkey";
    const XML_PATH_RECIPIENT_EMAIL = "productguide/view/recipient_email";
    const XML_PATH_EMAIL_IDENTITY = 'productguide/view/contact_sender_identity';
    const XML_PATH_MAIN_BANNER = "productguide/view/main_banner";
    /*@codingStandardsIgnoreEnd*/

    /**
     * Contact Us Helper File
     *
     * @param \Magento\Framework\App\Helper\Context $context
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \Magento\Store\Model\StoreManagerInterface $storeInterface
     * @param \Magento\Customer\Model\Session $customerSession
     * @param CustomerViewHelper $customerViewHelper
     */
    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Store\Model\StoreManagerInterface $storeInterface,
        \Magento\Customer\Model\Session $customerSession,
        CustomerViewHelper $customerViewHelper
    ) {
        $this->_scopeConfig = $scopeConfig;
        $this->_storeInterface = $storeInterface;
        $this->_customerSession = $customerSession;
        $this->_customerViewHelper = $customerViewHelper;
        parent::__construct($context);
    }

    /**
     * Get Store Config Path
     */
    public function isEnabled()
    {
        return $this->scopeConfig->isSetFlag(self::XML_PATH_ENABLE, \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
    }

    /**
     * Get Captcha sitekey
     */
    public function getCaptchaSitekey()
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_SITEKEY,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * Get Captcha secretkey
     */
    public function getCaptchaSecretkey()
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_SECRETKEY,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * Get recipientemail
     */
    public function getRecipientemail()
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_RECIPIENT_EMAIL,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * Get Email Identity
     */
    public function getEmailIdentity()
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_EMAIL_IDENTITY,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    public function getMediaPath()
    {
        $mediaUrl = $this->_storeInterface
                         ->getStore()
                         ->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA);

        return $mediaUrl;
    }

    public function getMainBanner(){
        return $this->getMediaPath().'wysiwyg/enquiry/'.$this->scopeConfig->getValue(self::XML_PATH_MAIN_BANNER, \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
    }

    /**
     * Get value from POST by key
     *
     * @param string $key
     * @return string
     */
    public function getPostValue($key)
    {
        if (null === $this->postData) {
            $this->postData = (array) $this->getDataPersistor()->get('productguide');
            $this->getDataPersistor()->clear('productguide');
        }
        if (isset($this->postData[$key])) {
            return (string) $this->postData[$key];
        }
        return '';
    }

    /**
     * Get getDataPersistor
     */
    private function getDataPersistor()
    {
        if ($this->dataPersistor === null) {
            $this->dataPersistor = ObjectManager::getInstance()
                ->get(DataPersistorInterface::class);
        }
        return $this->dataPersistor;
    }

    /**
     * Get user name
     *
     * @return string
     */
    public function getFirstname()
    {
        if (!$this->_customerSession->isLoggedIn()) {
            return '';
        }
        /**
         * @var \Magento\Customer\Api\Data\CustomerInterface $customer
         */
        $customer = $this->_customerSession->getCustomerDataObject();

        return trim($customer->getFirstname());
    }

    public function getLastname()
    {
        if (!$this->_customerSession->isLoggedIn()) {
            return '';
        }
        /**
         * @var \Magento\Customer\Api\Data\CustomerInterface $customer
         */
        $customer = $this->_customerSession->getCustomerDataObject();

        return trim($customer->getLastname());
    }

    /**
     * Get user email
     *
     * @return string
     */
    public function getUserEmail()
    {
        if (!$this->_customerSession->isLoggedIn()) {
            return '';
        }
        /**
         * @var CustomerInterface $customer
         */
        $customer = $this->_customerSession->getCustomerDataObject();

        return $customer->getEmail();
    }
}
