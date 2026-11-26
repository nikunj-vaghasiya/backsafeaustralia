<?php

namespace Digital\Productenquiry\Controller\Index;

use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Store\Model\ScopeInterface;
use Magento\Framework\App\Action\Context;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\App\Request\DataPersistorInterface;
use Magento\Framework\Serialize\SerializerInterface;
use Magento\Framework\Validator\ValidatorChain;
use Magento\Framework\Message\ManagerInterface;

class Save extends \Magento\Framework\App\Action\Action
{
    protected $_resultFactory;
    protected $_checkoutSession;
    protected $_urlBuilder;
    protected $_productenquiry;
    protected $_helperProductenquiry;
    private $dataPersistor;
    protected $_storeManager;
    protected $EmailHelper;
    protected $_productloader;
    protected $_transportBuilder;
    protected $_resourceconnection;
    protected $messageManager;
    protected $curl;

    /**
     * @var SerializerInterface
     */
    protected $serializer;

    /**
     * @var ValidatorChain
     */
    protected $validatorChain;

    public const XML_PATH_CUSTOMER_EMAIL  = 'productenquiry/view/email_template';
    public const XML_PATH_ADMIN_EMAIL  = 'productenquiry/view/admin_email';
    public const XML_PATH_EMAIL_SENDER = 'productenquiry/email/sender_email_identity';

    protected $session;

    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Framework\Controller\ResultFactory $resultFactory,
        \Magento\Checkout\Model\Session $checkoutSession,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\UrlInterface $urlBuilder,
        \Digital\Productenquiry\Helper\Data $helperProductenquiry,
        \Digital\Productenquiry\Model\Productenquiry $productenquiry,
        \Digital\Productenquiry\Helper\Email $emailHelper,
        \Magento\Catalog\Model\ProductFactory $_productloader,
        DataPersistorInterface $dataPersistor,
        \Magento\Framework\Mail\Template\TransportBuilder $transportBuilder,
        \Magento\Framework\Session\SessionManagerInterface $session,
        \Magento\Framework\App\ResourceConnection $resourceconnection,
        SerializerInterface $serializer,
        ValidatorChain $validatorChain,
        ManagerInterface $messageManager,
        \Magento\Framework\HTTP\Client\Curl $curl,
    ) {
        parent::__construct($context);
        $this->resultFactory = $resultFactory;
        $this->_checkoutSession = $checkoutSession;
        $this->_storeManager = $storeManager;
        $this->_urlBuilder = $urlBuilder;
        $this->dataPersistor = $dataPersistor;
        $this->_productenquiry = $productenquiry;
        $this->_helperProductenquiry = $helperProductenquiry;
        $this->EmailHelper = $emailHelper;
        $this->_productloader = $_productloader;
        $this->_transportBuilder = $transportBuilder;
        $this->session = $session;
        $this->_resourceconnection = $resourceconnection;
        $this->serializer = $serializer;
        $this->validatorChain = $validatorChain;
        $this->messageManager = $messageManager;
        $this->curl = $curl;
    }

    public function execute()
    {
        date_default_timezone_set('Australia/Sydney');

        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
        $post = $this->getRequest()->getPostValue();
        $email = $this->getRequest()->getPost('email');
        $site_name = $this->getRequest()->getPost('site_name');
        $first_name = $this->getRequest()->getPost('first_name');
        $last_name = $this->getRequest()->getPost('last_name');
        $company = $this->getRequest()->getPost('company');
        $phone = $this->getRequest()->getPost('phone');
        $state = $this->getRequest()->getPost('state');
        $postcode = $this->getRequest()->getPost('postcode');
        /* $subscribe = $this->getRequest()->getPost('subscribe'); */
        $comment = $this->getRequest()->getPost('message');

        if (!$post) {

            $this->_redirect('*/*/');

            return;
        } else {
            try {
                $error = false;

                if (!isset($first_name)) {
                    $validator->addValidator(new NotEmpty());
                    if (!$validator->isValid(trim($first_name))) {
                        $error = true;
                    }
                    $validator->resetValidators();
                }

                if (!isset($last_name)) {
                    $validator->addValidator(new NotEmpty());
                    if (!$validator->isValid(trim($last_name))) {
                        $error = true;
                    }
                    $validator->resetValidators();
                }

                if (!isset($postcode)) {
                    $validator->addValidator(new NotEmpty());
                    if (!$validator->isValid(trim($postcode))) {
                        $error = true;
                    }
                    $validator->resetValidators();
                }

                if (!isset($phone)) {
                    $validator->addValidator(new NotEmpty());
                    if (!$validator->isValid(trim($phone))) {
                        $error = true;
                    }
                    $validator->resetValidators();
                }

                if (!isset($email)) {
                    $validator->addValidator(new EmailAddress());
                    if (!$validator->isValid(trim($email))) {
                        $error = true;
                    }
                    $validator->resetValidators();
                }

                if (strpos(trim($post['email']), '@mail.ru') !== false || strpos(trim($post['email']), '@inbox.ru') !== false || strpos(trim($post['email']), '@bk.ru') !== false || strpos(trim($post['email']), '@list.ru') !== false || strpos(trim($post['email']), '@bluemail.ch') !== false || strpos(trim($post['email']), '@icloud.com') !== false || strpos(trim($post['email']), '@teachers-exchange.com') !== false || strpos(trim($post['email']), '@severen.net') !== false || strpos(trim($post['email']), '@mailontherail.net') !== false || strpos(trim($post['email']), '@msn.com') !== false || strpos(trim($post['email']), '@gmailere.com') !== false || strpos(trim($post['email']), '@excite.com') !== false || strpos(trim($post['email']), '@kashnkarry.com') !== false || strpos(trim($post['email']), '@tokkabanshop.com') !== false || strpos(trim($post['email']), '@netvigator.com') !== false) {

                    return $this->_redirectReferer(Mage::getUrl('*/*'));
                }

                if ($error) {
                    throw new \Exception();
                }

                $postObject = new \Magento\Framework\DataObject();
                $postObject->setData($post);
                $receiveremail = $this->_helperProductenquiry->Recipientemail();
                $receiverInfo = [
                    'name' => 'Quote Request',
                    'email' => $receiveremail,
                ];
                $sendername = $this->_helperProductenquiry->GeneralName();
                $senderemail = $this->_helperProductenquiry->GeneralEmail();
                $senderInfo = [
                    'name' => $sendername,
                    'email' => $senderemail,
                ];

                $senderInfo1 = [
                    'name' => $first_name,
                    'email' => $email,
                ];

                $customerInfo = [
                    'name' =>   $first_name,
                    'email' => $email,

                ];

                $products_array = [];
                $individualHtml = "";
                $hubspot_quote_details = "";
                $new_hubspot_quote_details = "";
                $order_sub_total = 0.00;

                if ($this->_checkoutSession->getEnquiryQuote()) {
                    $newQuoteProductsArray = $this->_checkoutSession->getEnquiryQuote();
                    $products = serialize($newQuoteProductsArray);
                    $this->_productenquiry->setData('products', $products);
                    $this->_checkoutSession->unsEnquiryQuote();

                    foreach ($newQuoteProductsArray as $productdata) {
                        $product_id = $productdata['product_id'];
                        $product_data = $this->_productloader->create()->load($product_id);
                        $individualHtml .= $productdata['name'] . " [" . $productdata['sku'] . "] " . " (" . $productdata['qty'] . ")" . "\n";
                        $hubspot_quote_details .= $productdata['name'] . " Qty:" . $productdata['qty'] . " | ";
                    }
                    $new_prod_data = preg_replace("/\r\n|\r|\n/", '<br/>', $individualHtml);
                    $postObject['products'] = $new_prod_data;
                    $new_hubspot_quote_details = rtrim($hubspot_quote_details, " | ");
                }

               /*  if ($subscribe == 1) {
                    $postObject['ship_outside_australia'] = 'Yes';
                    $ship_outside_australia = 'True';
                } else {
                    $postObject['ship_outside_australia'] = 'No';
                    $ship_outside_australia = 'False';
                } */

                $this->_productenquiry->setData('email', $email);
                $this->_productenquiry->setData('first_name', $first_name);
                $this->_productenquiry->setData('last_name', $last_name);
                $this->_productenquiry->setData('company', $company);
                $this->_productenquiry->setData('phone_number', $phone);
                $this->_productenquiry->setData('state', $state);
                $this->_productenquiry->setData('site_name', $site_name);
                $this->_productenquiry->setData('postcode', $postcode);
                /* $this->_productenquiry->setData('ship_outside_australia', $subscribe); */
                $this->_productenquiry->setData('message', $comment);

                $this->_productenquiry->setData('created_at', date("Y-m-d H:i:s"));

                $this->_productenquiry->save();
                $quote_save_id = $this->_productenquiry->getEnquiryId();
                $payload = [
                    "fields" => [
                        ["name" => "email",     "value" => $email],
                        ["name" => "firstname", "value" => $first_name],
                        ["name" => "lastname", "value" => $last_name],
                        ["name" => "company", "value" => $company],
                        ["name" => "phone",     "value" => $phone],
                        ["name" => "state",     "value" => $state],
                        ["name" => "message",   "value" => $comment],
                       /*  ["name" => "ship_outside_australia_check",   "value" => $ship_outside_australia], */
                        ["name" => "channe",   "value" => 'Organic search'],
                        ["name" => "channeldrilldown1_",   "value" => 'Google'],
                        ["name" => "channeldrilldown2_",   "value" => 'www.google.com'],
                        ["name" => "channeldrilldown3_",   "value" => 'None'],
                        ["name" => "landingpage_",   "value" => $this->_storeManager->getStore()->getBaseUrl()],
                        ["name" => "landingpagegroup_",   "value" => '/'],
                        ["name" => "campaignName",   "value" => 'CampaignName'],
                        ["name" => "quote_request_full_name",   "value" => $first_name . ' ' . $last_name],
                        ["name" => "quote_request_quote_number",   "value" => (string)$quote_save_id],
                        ["name" => "quote_request_company_name",   "value" => strip_tags($company)],
                        ["name" => "quote_request_quote_summary",   "value" => strip_tags(html_entity_decode($new_hubspot_quote_details))]
                    ]
                ];
                $jsonData = json_encode($payload);
                $hubspot_status = $this->sendToHubSpot($jsonData);

                $writer = new \Zend_Log_Writer_Stream(BP . '/var/log/Product_enquiry-25-11-2025-2.log');
                $logger = new \Zend_Log();
                $logger->addWriter($writer);
                $logger->info($jsonData);
                $logger->info("================================================================");
                $logger->info($hubspot_status);

                $this->dataPersistor->clear('productenquiry-form');
                $postObject['quote_save_id'] = $quote_save_id;
                $postObject['datetime'] = date("d/m/Y h:i:sa");
                $this->EmailHelper->customerMailSendMethod($postObject, $senderInfo, $customerInfo);
                $this->EmailHelper->adminEnquiryMailSendMethod($postObject, $senderInfo, $receiverInfo);
                $this->session->start();
                $this->session->setQuote(1);
                $this->session->setQuoteSaveId((int)$quote_save_id);
                $this->messageManager->addSuccess(__('Thank you for your quote request. Our team has received your details and will get back to you shortly.'));
                $this->session->unsQuote();
                $this->_redirect('*/*/success');
                return;
            } catch (\Exception $e) {
                $this->messageManager->addError(__('We can\'t process your request right now.'.$e->getMessage()));
                $this->dataPersistor->set('productenquiry-form', $this->getRequest()->getParams());
            }
        }
        $resultRedirect->setUrl($this->_storeManager->getStore()->getBaseUrl());
        return $resultRedirect;
    }

    public function sendToHubSpot($jsonPayload)
    {
        $portalId = "7713081";
        $formGuid = "efb94d35-ffc0-479a-be5f-20ea9d575490";
        $hubspotApiUrl = "https://api.hsforms.com/submissions/v3/integration/submit/{$portalId}/{$formGuid}";

        $ch = curl_init($hubspotApiUrl);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonPayload);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json'
        ]);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $curlError = curl_error($ch);
        curl_close($ch);

        // Enhanced logging
        $writer = new \Zend_Log_Writer_Stream(BP . '/var/log/hubspot_debug-2.log');
        $logger = new \Zend_Log();
        $logger->addWriter($writer);
        $logger->info("HTTP Code: " . $httpCode);
        $logger->info("Response: " . $response);
        if ($curlError) {
            $logger->info("CURL Error: " . $curlError);
        }

        if ($httpCode == 200) {
            return $response;
        } else {
            return "Error submitting form (HTTP {$httpCode}): " . $response;
        }
    }
}
