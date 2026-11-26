<?php
namespace Digital\Productenquiry\Observer;

use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\App\Request\DataPersistorInterface;
use Magento\Framework\App\ObjectManager;

class CheckCaptchaEnquiryObserver implements ObserverInterface
{
    protected $_helper;
    protected $_actionFlag;
    protected $messageManager;
    protected $_session;
    protected $_urlManager;
    protected $captchaStringResolver;
    protected $redirect;
    private $dataPersistor;
    public function __construct(
        \Magento\Captcha\Helper\Data $helper, //
        \Magento\Framework\App\ActionFlag $actionFlag, //
        \Magento\Framework\Message\ManagerInterface $messageManager, //
        \Magento\Framework\Session\SessionManagerInterface $session, //
        \Magento\Framework\UrlInterface $urlManager, //
        \Magento\Framework\App\Response\RedirectInterface $redirect, //
        \Magento\Captcha\Observer\CaptchaStringResolver $captchaStringResolver //
    ) {
        $this->_helper = $helper;
        $this->_actionFlag = $actionFlag;
        $this->messageManager = $messageManager;
        $this->_session = $session;
        $this->_urlManager = $urlManager;
        $this->redirect = $redirect;
        $this->captchaStringResolver = $captchaStringResolver;
    }
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $formId = 'productenquiry-form'; // this form ID should matched the one defined in the layout xml
        $captchaModel = $this->_helper->getCaptcha($formId);
            $controller = $observer->getControllerAction();
        if (!$captchaModel->isCorrect($this->captchaStringResolver->resolve($controller->getRequest(), $formId))) {
            $this->messageManager->addError(__('Incorrect CAPTCHA'));
            $this->getDataPersistor()->set($formId, $controller->getRequest()->getPostValue());
            $this->_actionFlag->set('', \Magento\Framework\App\Action\Action::FLAG_NO_DISPATCH, true);
            $this->_session->setCustomerFormData($controller->getRequest()->getPostValue());
            $url = $this->_urlManager->getUrl('productenquiry', ['_nosecret' => true]);
            $controller->getResponse()->setRedirect($this->redirect->error($url));
        }
        return $this;
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
