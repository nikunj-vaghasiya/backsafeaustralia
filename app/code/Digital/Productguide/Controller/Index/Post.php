<?php

namespace Digital\Productguide\Controller\Index;

use Magento\Framework\App\Action\Context;
use Magento\Framework\App\Request\DataPersistorInterface;
use Magento\Framework\Exception\LocalizedException;

class Post extends \Magento\Framework\App\Action\Action
{
    /**
     * Productguide Post Data Controller
     *
     * @param Context $context
     * @param Productguide $productguideFactory
     * @param Data $helper
     * @param Data $baseHelper
     * @param DataPersistorInterface $dataPersistor
     * @param Email $emailHelper
     */
    protected $productguideFactory;
    protected $helper;
    protected $baseHelper;
    protected $dataPersistor;
    protected $emailHelper;
    protected $curl;

    public function __construct(
        Context $context,
        \Digital\Productguide\Model\ProductguideFactory $productguideFactory,
        \Digital\Productguide\Helper\Data $helper,
        \Digital\Base\Helper\Data $baseHelper,
        DataPersistorInterface $dataPersistor,
        \Digital\Productguide\Helper\Email $emailHelper,
        \Magento\Framework\HTTP\Client\Curl $curl
    ) {
        parent::__construct($context);
        $this->productguideFactory = $productguideFactory;
        $this->helper = $helper;
        $this->baseHelper = $baseHelper;
        $this->dataPersistor = $dataPersistor;
        $this->emailHelper = $emailHelper;
        $this->curl = $curl;
    }

    /**
     * Execute Method For Saving Form Data
     *
     * @return void
     */
    public function execute()
    {
        if (!$this->getRequest()->isPost()) {
            return $this->resultRedirectFactory->create()->setPath('*/*/');
        }

        try {
            $params = $this->validatedParams();
            if ($params) {
                $this->dataPersistor->set('productguide', $params);
            }
            $contact = $this->productguideFactory->create();
            $contact->setData($params);
            $contact->setCreatedAt(date('Y-m-d H:i:s'));
            $contact->save();

            if ($params['catalogue_checkbox'] == 1) {
                $received_catalogue = 'Yes';
            } else {
                $received_catalogue = 'No';
            }

            $fields = [
                ["name" => "firstname", "value" => $params['firstname']],
                ["name" => "lastname", "value" => $params['lastname']],
                ["name" => "company", "value" => $params['company']],
                ["name" => "email",     "value" => $params['email']],
                ["name" => "receive_printed_guide_by_mail", "value" => $received_catalogue]
            ];
            if ($params['catalogue_checkbox'] == 1) {
                array_push(
                    $fields,
                    ["name" => "address", "value" => $params['street']],
                    ["name" => "city", "value" => $params['city']],
                    ["name" => "state", "value" => $params['state']],
                    ["name" => "zip", "value" => $params['postcode']],
                    ["name" => "phone", "value" => $params['phone']]
                );
            }
            $payload = ["fields" => $fields];
            $jsonData = json_encode($payload);

            $hubSpot_status = $this->sendToHubSpot($jsonData);


            $writer = new \Zend_Log_Writer_Stream(BP . '/var/log/Product_Post-25-11-2025.log');
            $logger = new \Zend_Log();
            $logger->addWriter($writer);            
            $logger->info($jsonData);
            $logger->info("HubSpot Status: " . $hubSpot_status);

            /* Email send call */
            $this->sendEmail($params);

            $this->messageManager->addSuccessMessage(__(
                'Your enquiry has been submitted and will be responded to as soon as possible.'
            ));

            $this->dataPersistor->clear('productguide');
        } catch (LocalizedException $e) {
            $this->messageManager->addErrorMessage(__($e->getMessage()));
            $this->dataPersistor->set('productguide', $this->getRequest()->getParams());
        } catch (\Exception $e) {
            $this->messageManager->addErrorMessage(__(
                'We can\'t process your request right now. Sorry, that\'s all we know.' . $e->getMessage()
            ));
            $this->dataPersistor->set('productguide', $this->getRequest()->getParams());
        }

        return $this->resultRedirectFactory->create()->setPath('resources/catalogue-downloads');
    }

    /**
     * Validate Parameters
     *
     * @return array
     * @throws \Exception
     */
    private function validatedParams()
    {
        $request = $this->getRequest();
        if (trim($request->getParam('firstname') ?? '') === '') {
            throw new LocalizedException(__('Enter the First Name and try again.'));
        }
        if (trim($request->getParam('lastname') ?? '') === '') {
            throw new LocalizedException(__('Enter the Last Name and try again.'));
        }
        if (false === \strpos($request->getParam('email'), '@')) {
            throw new LocalizedException(__('The email address is invalid. Verify the email address and try again.'));
        }
        if (trim($request->getParam('hideit')) !== '') {
            throw new LocalizedException(__('Sorry, you are not allowed to place product guide enquiry. please try later.'));
        }

        /* Validate bad characters */
        $contactDetails = [
            trim($request->getParam('firstname') ?? ''),
            trim($request->getParam('email') ?? ''),
        ];

        if ($this->baseHelper->getBadcharacter($contactDetails)) {
            throw new LocalizedException(
                __('Sorry, you are not allowed to place product guide enquiry. please try later.')
            );
        }

        if (
            $this->baseHelper->isNonEnglishString(trim($request->getParam('firstname') ?? ''))
            || $this->baseHelper->isNonEnglishString(trim($request->getParam('email') ?? ''))
        ) {
            throw new LocalizedException(
                __('Sorry, you are not allowed to place product guide enquiry. please try later.')
            );
        }
        /* Validate bad characters */

        return $request->getParams();
    }

    /**
     * Function for Sending Email
     *
     * @param Post $post
     * @return void
     */
    public function sendEmail($post)
    {
        $postObj = new \Magento\Framework\DataObject();
        $postObj->setData($post);
        $toemail = preg_replace('/\s*,\s*/', ',', $this->helper->getRecipientemail() ?? '');
        if (strpos($toemail, ',') !== false) {
            $emails = explode(',', $toemail);
            $toemail = $emails[0];
        }

        $receiverInfo = ['name' => 'Productguide', 'email' => $toemail];
        $senderInfo = $this->helper->getEmailIdentity();
        $customerInfo = ['name' => $postObj->getFirstname(), 'email' => (trim($postObj->getEmail() ?? ''))];

        /*Email to admin*/
        $this->emailHelper->checkAndSend($postObj, $senderInfo, $receiverInfo);
        /*Email to customer*/
        $this->emailHelper->contactMailSendMethodToCustomer($postObj, $senderInfo, $customerInfo);
    }


    public function sendToHubSpot($jsonPayload)
    {
        $portalId = '7713081';
        $formGuid = 'a7c163bb-23e5-4747-bde8-e64f0b2b58ad';
        $hubspotApiUrl = "https://api.hsforms.com/submissions/v3/integration/submit/{$portalId}/{$formGuid}";

        $ch = curl_init($hubspotApiUrl);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonPayload);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json' // Use if using Private Apps with bearer token
        ]);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($httpCode == 200) {
            return $response;
        } else {
            return "Error submitting form: " . $response;
        }
    }
}
