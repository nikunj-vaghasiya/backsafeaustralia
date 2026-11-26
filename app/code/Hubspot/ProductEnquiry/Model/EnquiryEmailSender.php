<?php
namespace Hubspot\ProductEnquiry\Model;

use Magento\Framework\App\Area;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\DataObjectFactory;
use Magento\Framework\Mail\Template\TransportBuilder;
use Magento\Framework\Translate\Inline\StateInterface;
use Magento\Store\Model\ScopeInterface;
use Magento\Store\Model\StoreManagerInterface;
use Psr\Log\LoggerInterface;

/**
 * Sends notification emails for a product enquiry:
 *  - one to the customer (acknowledgement)
 *  - one to the admin/sales inbox (notification)
 */
class EnquiryEmailSender
{
    const XML_ENABLED     = 'hubspot_productenquiry/email/enabled';
    const XML_SENDER      = 'hubspot_productenquiry/email/sender';
    const XML_ADMIN_EMAIL = 'hubspot_productenquiry/email/admin_email';

    const ADMIN_TEMPLATE    = 'hubspot_productenquiry_admin_template';
    const CUSTOMER_TEMPLATE = 'hubspot_productenquiry_customer_template';

    /** @var TransportBuilder */
    private $transportBuilder;
    /** @var StateInterface */
    private $inlineTranslation;
    /** @var StoreManagerInterface */
    private $storeManager;
    /** @var ScopeConfigInterface */
    private $scopeConfig;
    /** @var LoggerInterface */
    private $logger;

    /** @var DataObjectFactory */
    private $dataObjectFactory;

    public function __construct(
        TransportBuilder $transportBuilder,
        StateInterface $inlineTranslation,
        StoreManagerInterface $storeManager,
        ScopeConfigInterface $scopeConfig,
        LoggerInterface $logger,
        DataObjectFactory $dataObjectFactory
    ) {
        $this->transportBuilder = $transportBuilder;
        $this->inlineTranslation = $inlineTranslation;
        $this->storeManager = $storeManager;
        $this->scopeConfig = $scopeConfig;
        $this->logger = $logger;
        $this->dataObjectFactory = $dataObjectFactory;
    }

    /**
     * @param array $data keys: name, email, phone, enquiry, comments, created_at
     */
    public function send(array $data): void
    {
        try {
            $storeId = (int) $this->storeManager->getStore()->getId();

            if (!$this->scopeConfig->isSetFlag(self::XML_ENABLED, ScopeInterface::SCOPE_STORE, $storeId)) {
                return;
            }

            $sender = $this->scopeConfig->getValue(self::XML_SENDER, ScopeInterface::SCOPE_STORE, $storeId) ?: 'general';

            $vars = [
                'name'       => $data['name'] ?? '',
                'email'      => $data['email'] ?? '',
                'phone'      => $data['phone'] ?? '',
                'enquiry'    => $data['enquiry'] ?? '',
                'comments'   => $data['comments'] ?? '',
                'product'    => $data['product'] ?? '',
                'product_name' => $data['product_name'] ?? '',
                'product_url'  => $data['product_url'] ?? '',
                'created_at' => $data['created_at'] ?? '',
            ];

            // Pass flat vars ({{var name}}) plus a data object ({{var data.name}})
            // so templates work with either syntax.
            $dataObject = $this->dataObjectFactory->create(['data' => $vars]);
            $templateVars = array_merge($vars, ['data' => $dataObject]);

            // Admin notification
            $adminEmail = $this->scopeConfig->getValue(self::XML_ADMIN_EMAIL, ScopeInterface::SCOPE_STORE, $storeId);
            if (!$adminEmail) {
                $adminEmail = $this->scopeConfig->getValue(
                    'trans_email/ident_general/email',
                    ScopeInterface::SCOPE_STORE,
                    $storeId
                );
            }
            if ($adminEmail) {
                $this->sendOne(self::ADMIN_TEMPLATE, $sender, $adminEmail, 'Administrator', $templateVars, $storeId);
            }

            // Customer acknowledgement
            if (!empty($data['email'])) {
                $this->sendOne(
                    self::CUSTOMER_TEMPLATE,
                    $sender,
                    $data['email'],
                    $data['name'] ?: 'Customer',
                    $templateVars,
                    $storeId
                );
            }
        } catch (\Throwable $e) {
            // Never let email failure break the webhook.
            $this->logger->error('ProductEnquiry email send failed: ' . $e->getMessage());
        }
    }

    private function sendOne(string $templateId, string $sender, string $toEmail, string $toName, array $vars, int $storeId): void
    {
        try {
            $this->inlineTranslation->suspend();
            $transport = $this->transportBuilder
                ->setTemplateIdentifier($templateId)
                ->setTemplateOptions(['area' => Area::AREA_FRONTEND, 'store' => $storeId])
                ->setTemplateVars($vars)
                ->setFromByScope($sender, $storeId)
                ->addTo($toEmail, $toName)
                ->getTransport();
            $transport->sendMessage();
        } catch (\Throwable $e) {
            $this->logger->error('ProductEnquiry email to ' . $toEmail . ' failed: ' . $e->getMessage());
        } finally {
            $this->inlineTranslation->resume();
        }
    }
}