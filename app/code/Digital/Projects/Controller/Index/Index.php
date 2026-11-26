<?php

namespace Digital\Projects\Controller\Index;

use Magento\Framework\App\Action\Action;
use Magento\Framework\Controller\ResultFactory;

class Index extends \Magento\Framework\App\Action\Action
{
    protected $resultPageFactory;
    protected $_helper;

    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Digital\Projects\Helper\Data $_helper,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory
    ) {
        $this->resultPageFactory = $resultPageFactory;
        $this->_helper = $_helper;
        parent::__construct($context);
    }
    public function execute()
    {
        if (!$this->_helper->isEnabled()) {
            $this->_redirect('noRoute');
            return;
        } else {
            $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
            $resultPage = $this->resultPageFactory->create();
            return $resultPage;
        }
    }
}
