<?php
declare(strict_types=1);

namespace Digital\Ourteam\Controller\Index;

use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\Action\HttpGetActionInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Controller\Result\ForwardFactory;
use Magento\Framework\View\Result\PageFactory;
use Magento\Store\Model\ScopeInterface;

class Index extends Action implements HttpGetActionInterface
{
    /** Enable config path */
    public const XML_PATH_ENABLED = 'ourteam_section/general/enable';

    /**
     * @var PageFactory
     */
    private $resultPageFactory;

    /**
     * @var ForwardFactory
     */
    private $resultForwardFactory;

    /**
     * @var ScopeConfigInterface
     */
    private $scopeConfig;

    /**
     * @param Context              $context
     * @param PageFactory          $resultPageFactory
     * @param ForwardFactory       $resultForwardFactory
     * @param ScopeConfigInterface $scopeConfig
     */
    public function __construct(
        Context $context,
        PageFactory $resultPageFactory,
        ForwardFactory $resultForwardFactory,
        ScopeConfigInterface $scopeConfig
    ) {
        parent::__construct($context);
        $this->resultPageFactory = $resultPageFactory;
        $this->resultForwardFactory = $resultForwardFactory;
        $this->scopeConfig = $scopeConfig;
    }

    /**
     * @return \Magento\Framework\Controller\Result\Forward|\Magento\Framework\View\Result\Page
     */
    public function execute()
    {
        if (!$this->scopeConfig->isSetFlag(self::XML_PATH_ENABLED, ScopeInterface::SCOPE_STORE)) {
            return $this->resultForwardFactory->create()->forward('noroute');
        }

        $resultPage = $this->resultPageFactory->create();
        $resultPage->getConfig()->getTitle()->set(__('Our Team'));

        return $resultPage;
    }
}