<?php
namespace Hubspot\ProductEnquiry\Controller\Webhook;

use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\Action\HttpPostActionInterface;
use Magento\Framework\App\CsrfAwareActionInterface;
use Magento\Framework\App\Request\InvalidRequestException;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Framework\Serialize\Serializer\Json;
use Hubspot\ProductEnquiry\Model\EnquiryFactory;
use Hubspot\ProductEnquiry\Model\EnquiryEmailSender;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Psr\Log\LoggerInterface;

/**
 * Receives the "Send a webhook" POST from a HubSpot workflow.
 *
 * URL to paste into the HubSpot webhook action:
 *   https://YOUR-DOMAIN/productenquiry/webhook/index?token=THE_SECRET_BELOW
 */
class Index extends Action implements HttpPostActionInterface, CsrfAwareActionInterface
{
    /**
     * Change this to a long random string, then put the SAME value in the
     * HubSpot webhook URL as ?token=...  This is what proves the request
     * really came from your HubSpot workflow.
     */
    const SHARED_SECRET = 'c0f0a9739416dd2e36bc560d1cd21866b9db7571a49e5dc9';

    /** @var JsonFactory */
    protected $resultJsonFactory;

    /** @var EnquiryFactory */
    protected $enquiryFactory;

    /** @var Json */
    protected $json;

    /** @var LoggerInterface */
    protected $logger;

    /** @var EnquiryEmailSender */
    protected $emailSender;

    /** @var ProductRepositoryInterface */
    protected $productRepository;

    public function __construct(
        Context $context,
        JsonFactory $resultJsonFactory,
        EnquiryFactory $enquiryFactory,
        Json $json,
        LoggerInterface $logger,
        EnquiryEmailSender $emailSender,
        ProductRepositoryInterface $productRepository
    ) {
        parent::__construct($context);
        $this->resultJsonFactory = $resultJsonFactory;
        $this->enquiryFactory = $enquiryFactory;
        $this->json = $json;
        $this->logger = $logger;
        $this->emailSender = $emailSender;
        $this->productRepository = $productRepository;
    }

    public function createCsrfValidationException(RequestInterface $request): ?InvalidRequestException
    {
        return null;
    }

    public function validateForCsrf(RequestInterface $request): ?bool
    {
        // HubSpot has no Magento form key, so skip CSRF; the ?token= check below
        // is what actually authenticates the request.
        return true;
    }

    public function execute()
    {
        $result = $this->resultJsonFactory->create();

        // 1) Authenticate: the secret token must match.
        $token = (string) $this->getRequest()->getParam('token');
        if (self::SHARED_SECRET === 'CHANGE_ME_TO_A_LONG_RANDOM_STRING'
            || !hash_equals(self::SHARED_SECRET, $token)
        ) {
            $this->logger->warning('ProductEnquiry webhook: invalid or unconfigured token.');
            return $result->setHttpResponseCode(401)->setData(['success' => false]);
        }

        try {
            // 2) Read the raw JSON body that HubSpot POSTs.
            $body = (string) file_get_contents('php://input');
            $payload = $body !== '' ? $this->json->unserialize($body) : [];

            // HubSpot may send properties flat OR nested as {"value": "..."}.
            $props = isset($payload['properties']) && is_array($payload['properties'])
                ? $payload['properties']
                : $payload;

            $get = static function (array $props, array $keys) {
                foreach ($keys as $k) {
                    if (isset($props[$k])) {
                        $v = $props[$k];
                        if (is_array($v) && array_key_exists('value', $v)) {
                            $v = $v['value'];
                        }
                        if ($v !== '' && $v !== null && !is_array($v)) {
                            return $v;
                        }
                    }
                }
                return null;
            };

            // 3) Resolve the SKU (sent in "product") into a real product.
            $sku = $get($props, ['product', 'product_sku', 'recent_conversion']);
            $sku = ($sku !== null) ? trim((string) $sku) : null;

            $productId = null;
            $productName = null;
            $productUrl = null;
            if ($sku !== null && $sku !== '') {
                try {
                    $product = $this->productRepository->get($sku);
                    $productId   = (int) $product->getId();
                    $productName = $product->getName();
                    $productUrl  = $product->getProductUrl();
                } catch (NoSuchEntityException $e) {
                    // SKU not found (or the form sent a name fallback) -> keep raw value as the name.
                    $productName = $sku;
                }
            }

            // raw_data keeps the FULL payload so no field is ever lost.
            $enquiry = $this->enquiryFactory->create();
            $enquiry->addData([
                'name'         => $get($props, ['name', 'firstname', 'firstName']),
                'email'        => $get($props, ['email']),
                'phone'        => $get($props, ['phone', 'mobilephone', 'mobile']),
                'enquiry'      => $get($props, ['enquiry', 'message']),
                'comments'     => $get($props, ['comments']),
                'product'      => $sku,          // the SKU as submitted
                'product_id'   => $productId,
                'product_name' => $productName,
                'product_url'  => $productUrl,
                'raw_data'     => $body,
            ]);
            $enquiry->save();

            // 4) Notify admin + customer by email (failures are logged, never thrown).
            $this->emailSender->send([
                'name'       => $enquiry->getData('name'),
                'email'      => $enquiry->getData('email'),
                'phone'      => $enquiry->getData('phone'),
                'enquiry'    => $enquiry->getData('enquiry'),
                'comments'   => $enquiry->getData('comments'),
                'product'      => $enquiry->getData('product'),
                'product_name' => $enquiry->getData('product_name'),
                'product_url'  => $enquiry->getData('product_url'),
                'created_at' => $enquiry->getData('created_at'),
            ]);

            // 5) Return 2xx quickly so HubSpot does not retry.
            return $result->setData(['success' => true, 'id' => $enquiry->getId()]);
        } catch (\Throwable $e) {
            $this->logger->critical('ProductEnquiry webhook failed: ' . $e->getMessage());
            // 200 to avoid endless retries of an unparseable payload; details are logged.
            return $result->setHttpResponseCode(200)->setData(['success' => false]);
        }
    }
    /**
     * HubSpot "Recent Conversion" arrives as "{Page Title} | {Brand}...: {Form Name}".
     * Keep just the product/page title (text before the first " | " brand separator),
     * falling back to stripping the known site/form suffixes.
     */
    private function cleanProduct($raw): ?string
    {
        if ($raw === null || $raw === '') {
            return null;
        }
        $product = (string) $raw;
        $pos = strpos($product, ' | ');
        if ($pos !== false) {
            $product = substr($product, 0, $pos);
        } else {
            $product = preg_replace('/:\s*Product Enquiry Form\s*$/i', '', $product);
            $product = preg_replace('/\s*-\s*Backsafe Australia\s*$/i', '', $product);
        }
        $product = trim($product);
        return $product !== '' ? $product : null;
    }
}