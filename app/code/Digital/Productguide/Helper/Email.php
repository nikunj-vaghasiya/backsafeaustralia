<?php
namespace Digital\Productguide\Helper;

/**
 * Custom Module Email helper
 */
class Email extends \Magento\Framework\App\Helper\AbstractHelper
{
    /*@codingStandardsIgnoreStart*/
    const XML_PATH_EMAIL_TEMPLATE_FIELD_CUSTOMER = 'productguide/view/email_template';
    const XML_PATH_EMAIL_TEMPLATE_FIELD_ADMIN = 'productguide/view/admin_email';
    const XML_PATH_EMAIL_COPY_METHOD = 'productguide/view/copy_method';
    const XML_PATH_EMAIL_COPY_TO = 'productguide/view/recipient_email';
    const XML_PATH_RECIPIENT_EMAIL = "productguide/view/recipient_email";
    /*@codingStandardsIgnoreEnd*/

    /* Here section and group refer to name of section and group where you create this field in configuration*/
    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $_scopeConfig;

    /**
     * To get storename
     *
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * @var \Magento\Framework\Translate\Inline\StateInterface
     */
    protected $inlineTranslation;

    /**
     * @var \Magento\Framework\Mail\Template\TransportBuilder
     */
    protected $_transportBuilder;

    /**
     * @var string
     */
    protected $temp_id;

    /**
     * @param Magento\Framework\App\Helper\Context $context
     * @param Magento\Store\Model\StoreManagerInterface $storeManager
     * @param Magento\Framework\Translate\Inline\StateInterface $inlineTranslation
     * @param Magento\Framework\Mail\Template\TransportBuilder $transportBuilder
     */
    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\Translate\Inline\StateInterface $inlineTranslation,
        \Magento\Framework\Mail\Template\TransportBuilder $transportBuilder
    ) {
        $this->_scopeConfig = $context;
        parent::__construct($context);
        $this->_storeManager = $storeManager;
        $this->inlineTranslation = $inlineTranslation;
        $this->_transportBuilder = $transportBuilder;
    }

    /**
     * Return store configuration value of your template field that which id you set for template
     *
     * @param string $path
     * @param int $storeId
     * @return mixed
     */
    public function getConfigValue($path, $storeId)
    {
        return $this->scopeConfig->getValue(
            $path,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    /**
     * Return store
     *
     * @return Store
     */
    public function getStore()
    {
        return $this->_storeManager->getStore();
    }

    /**
     * Return template id
     *
     * @param  Mixed $xmlPath
     * @return mixed
     */
    public function getTemplateId($xmlPath)
    {
        return $this->getConfigValue($xmlPath, $this->getStore()->getStoreId());
    }

    /**
     * Return email copy_to list
     *
     * @return array|bool
     */
    public function getEmailCopyTo()
    {
        $data = $this->getConfigValue(self::XML_PATH_EMAIL_COPY_TO, $this->getStore()->getStoreId());
        if (!empty($data)) {
            return array_map('trim', explode(',', $data));
        }
        return false;
    }

    /**
     * Get recipientemail
     */
    public function getRecipientemail()
    {
        return $this->getConfigValue(self::XML_PATH_RECIPIENT_EMAIL, $this->getStore()->getStoreId());
    }

    /**
     * Return copy method
     *
     * @return mixed
     */
    public function getCopyMethod()
    {
        return $this->getConfigValue(self::XML_PATH_EMAIL_COPY_METHOD, $this->getStore()->getStoreId());
    }

    /**
     * Send order email if it is enabled in configuration.
     *
     * @param EmailTemplateVariables $emailTemplateVariables
     * @param SenderInfo $senderInfo
     * @param ReceiverInfo $receiverInfo
     * @return bool
     */
    public function checkAndSend($emailTemplateVariables, $senderInfo, $receiverInfo)
    {
        /** @var SenderBuilder $sender */
        try {
            $this->send($emailTemplateVariables, $senderInfo, $receiverInfo);
        } catch (\Exception $e) {
            return false;
        }
        if ($this->getCopyMethod() == 'copy') {
            try {
                $this->sendCopyTo($emailTemplateVariables, $senderInfo);
            } catch (\Exception $e) {
                return false;
            }
        }
        return true;
    }

    /**
     * Prepare and send copy email message
     *
     * @param EmailTemplateVariables $emailTemplateVariables
     * @param SenderInfo $senderInfo
     * @return void
     */
    public function sendCopyTo($emailTemplateVariables, $senderInfo)
    {
        $copyTo = $this->getEmailCopyTo();
        if (!empty($copyTo)) {
            foreach ($copyTo as $email) {
                $this->configureEmailTemplate($emailTemplateVariables, $senderInfo);
                $this->_transportBuilder->addTo($email);
                $transport = $this->_transportBuilder->getTransport();
                $transport->sendMessage();
            }
        }
    }

    /**
     * Prepare and send email message
     *
     * @param EmailTemplateVariables $emailTemplateVariables
     * @param SenderInfo $senderInfo
     * @param ReceiverInfo $receiverInfo
     * @return void
     */
    public function send($emailTemplateVariables, $senderInfo, $receiverInfo)
    {
        $this->configureEmailTemplate($emailTemplateVariables, $senderInfo);
        $this->_transportBuilder->addTo($receiverInfo['email'], $receiverInfo['name']);
        $copyTo = $this->getEmailCopyTo();
        if (!empty($copyTo) && $this->getCopyMethod() == 'bcc') {
            foreach ($copyTo as $email) {
                $this->_transportBuilder->addBcc($email);
            }
        }

        $transport = $this->_transportBuilder->getTransport();
        $transport->sendMessage();
    }

    /**
     * Configure email template
     *
     * @param EmailTemplateVariables $emailTemplateVariables
     * @param SenderInfo $senderInfo
     * @return void
     */
    protected function configureEmailTemplate($emailTemplateVariables, $senderInfo)
    {
        $this->temp_id = $this->getTemplateId(self::XML_PATH_EMAIL_TEMPLATE_FIELD_ADMIN);
        $this->inlineTranslation->suspend();
        $this->_transportBuilder->setTemplateIdentifier($this->temp_id);
        $this->_transportBuilder->setTemplateOptions([
            'area' => \Magento\Framework\App\Area::AREA_FRONTEND,
            'store' => $this->_storeManager->getStore()->getId(),
        ]);
        $this->_transportBuilder->setTemplateVars(['data' => $emailTemplateVariables]);
        $this->_transportBuilder->setFrom($senderInfo);
    }

    /**
     * [generateTemplate description]  with template file and tempaltes variables values
     *
     * @param  Mixed $emailTemplateVariables
     * @param  Mixed $senderInfo
     * @param  Mixed $receiverInfo
     * @return void
     */
    public function generateTemplate($emailTemplateVariables, $senderInfo, $receiverInfo)
    {
        $template = $this->_transportBuilder->setTemplateIdentifier($this->temp_id)
            ->setTemplateOptions([
                'area' => \Magento\Framework\App\Area::AREA_FRONTEND,
                'store' => $this->_storeManager->getStore()->getId(),
            ])
            ->setTemplateVars(['data' => $emailTemplateVariables])
            ->setFrom($senderInfo)
            ->addTo($receiverInfo['email'], $receiverInfo['name']);
        return $this;
    }

    /**
     * Configure email template
     *
     * @param  EmailTemplateVariables $emailTemplateVariables
     * @param  SenderInfo $senderInfo
     * @param  ReceiverInfo $receiverInfo
     * @return void
     */
    public function contactMailSendMethodToCustomer($emailTemplateVariables, $senderInfo, $receiverInfo)
    {
        $this->temp_id = $this->getTemplateId(self::XML_PATH_EMAIL_TEMPLATE_FIELD_CUSTOMER);
        $this->inlineTranslation->suspend();
        $this->generateTemplate($emailTemplateVariables, $senderInfo, $receiverInfo);
        $transport = $this->_transportBuilder->getTransport();
        $transport->sendMessage();
        $this->inlineTranslation->resume();
    }
}
