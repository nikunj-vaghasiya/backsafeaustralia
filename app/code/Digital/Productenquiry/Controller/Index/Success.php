<?php
namespace Digital\Productenquiry\Controller\Index;

use Magento\Framework\View\Result\PageFactory;

class Success extends \Magento\Framework\App\Action\Action
{
    protected $resultPageFactory;
    protected $session;
    protected $resultRedirectFactory;

    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        PageFactory $resultPageFactory,
        \Magento\Framework\Session\SessionManagerInterface $session,
        \Magento\Framework\Controller\Result\RedirectFactory $resultRedirectFactory
    ) {
        $this->resultPageFactory = $resultPageFactory;
        $this->session = $session;
        $this->resultRedirectFactory = $resultRedirectFactory;
        parent::__construct($context);
    }

    public function execute()
    {
        $this->session->start();
        
        // Check if quote_save_id exists in session
        $quoteSaveId = $this->session->getQuoteSaveId();
        $quoteFlag = $this->session->getQuote();
        
        // If quote_save_id is empty, null, or 0, redirect to home page
        if (empty($quoteSaveId) || is_null($quoteSaveId) || $quoteSaveId <= 0) {
            $this->messageManager->addWarning(__('Invalid request. Please submit a quote inquiry to access this page.'));
            
            // Clear any session data
            $this->session->unsQuote();
            $this->session->unsQuoteSaveId();
            
            // Redirect to home page
            $resultRedirect = $this->resultRedirectFactory->create();
            return $resultRedirect->setUrl($this->_url->getBaseUrl());
        }
        
        // Valid quote - load and display success page
        $resultPage = $this->resultPageFactory->create();
        
        // Optional: Pass data to template
        if ($block = $resultPage->getLayout()->getBlock('productenquiry.success')) {
            $block->setQuoteSaveId($quoteSaveId);
        }
        
        return $resultPage;
    }
}
