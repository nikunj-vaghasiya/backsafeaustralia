<?php

namespace Digital\Productguide\Controller\Index;

use Magento\Framework\View\Result\PageFactory;

class Index extends \Magento\Framework\App\Action\Action
{
    /**
     * @var PageFactory
     */
    protected $resultPageFactory;
    /**
     * @var helper
     */
    protected $_helper;

    /**
     * @param \Magento\Framework\App\Action\Context $context
     * @param \Digital\Productguide\Helper\Data $_helper
     * @param PageFactory $resultPageFactory
     */
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Digital\Productguide\Helper\Data $_helper,
        PageFactory $resultPageFactory
    ) {
        $this->resultPageFactory = $resultPageFactory;
        $this->_helper = $_helper;
        parent::__construct($context);
    }

    /**
     * Default Productguide Index page
     *
     * @return void
     */
    public function execute()
    {
        /*@codingStandardsIgnoreStart*/
        if (!$this->_helper->isEnabled()) {
            $this->_redirect('noRoute');
            return;
        } else {
            $this->_view->loadLayout();
            /*@codingStandardsIgnoreEnd*/
            /** @var \Magento\Framework\View\Result\Page $resultPage */
            $resultPage = $this->resultPageFactory->create();
            return $resultPage;
        }
    }
}
