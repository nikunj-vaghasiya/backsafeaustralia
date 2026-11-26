<?php
namespace Hubspot\ProductEnquiry\Controller\Index;

use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\Action\HttpPostActionInterface;
use Magento\Framework\App\CsrfAwareActionInterface;
use Magento\Framework\App\Request\InvalidRequestException;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Controller\Result\JsonFactory;
use Hubspot\ProductEnquiry\Model\EnquiryFactory;
use Psr\Log\LoggerInterface;

class Save extends Action implements HttpPostActionInterface, CsrfAwareActionInterface
{
    /** @var JsonFactory */
    protected $resultJsonFactory;

    /** @var EnquiryFactory */
    protected $enquiryFactory;

    /** @var LoggerInterface */
    protected $logger;

    public function __construct(
        Context $context,
        JsonFactory $resultJsonFactory,
        EnquiryFactory $enquiryFactory,
        LoggerInterface $logger
    ) {
        parent::__construct($context);
        $this->resultJsonFactory = $resultJsonFactory;
        $this->enquiryFactory = $enquiryFactory;
        $this->logger = $logger;
    }

    /**
     * This endpoint receives data from a third-party (HubSpot) form callback,
     * so the Magento form_key is not present. CSRF validation is therefore
     * bypassed. See the README note about hardening this endpoint.
     */
    public function createCsrfValidationException(RequestInterface $request): ?InvalidRequestException
    {
        return null;
    }

    public function validateForCsrf(RequestInterface $request): ?bool
    {
        return true;
    }

    public function execute()
    {
        $result = $this->resultJsonFactory->create();

        try {
            $params = $this->getRequest()->getParams();
            unset($params['form_key'], $params['isAjax']);

            if (empty($params)) {
                return $result->setData(['success' => false, 'message' => 'No data received.']);
            }

            // Helper to read a field by any of several possible HubSpot names.
            $pick = static function (array $src, array $keys) {
                foreach ($keys as $k) {
                    if (!empty($src[$k])) {
                        return $src[$k];
                    }
                }
                return null;
            };

            $enquiry = $this->enquiryFactory->create();
            $enquiry->addData([
                'firstname' => $pick($params, ['firstname', 'firstName', 'first_name']),
                'lastname'  => $pick($params, ['lastname', 'lastName', 'last_name']),
                'email'     => $pick($params, ['email']),
                'phone'     => $pick($params, ['phone', 'mobilephone', 'mobile']),
                'company'   => $pick($params, ['company']),
                'message'   => $pick($params, ['message', 'comments', 'enquiry']),
                // Keep the complete payload so no field is ever lost,
                // regardless of how the HubSpot form is configured.
                'raw_data'  => json_encode($params, JSON_UNESCAPED_UNICODE),
            ]);
            $enquiry->save();

            return $result->setData(['success' => true, 'id' => $enquiry->getId()]);
        } catch (\Throwable $e) {
            $this->logger->critical('ProductEnquiry save failed: ' . $e->getMessage());
            return $result->setData(['success' => false, 'message' => 'Could not save enquiry.']);
        }
    }
}
