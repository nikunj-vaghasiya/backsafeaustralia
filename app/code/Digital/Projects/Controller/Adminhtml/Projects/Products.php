<?php

namespace Digital\Projects\Controller\Adminhtml\Projects;

use Magento\Backend\App\Action;
use Magento\TestFramework\ErrorLog\Logger;

/**
 * Class Products
 * @package Digital\Productattach\Controller\Adminhtml\Index
 */
class Products extends \Magento\Backend\App\Action
{
    /**
     * @var \Magento\Framework\View\Result\LayoutFactory
     */
    private $resultLayoutFactory;

    /**
     * Products constructor.
     * @param Action\Context $context
     * @param \Magento\Framework\View\Result\LayoutFactory $resultLayoutFactory
     */
    public function __construct(
        Action\Context $context,
        \Magento\Framework\View\Result\LayoutFactory $resultLayoutFactory
    ) {
        parent::__construct($context);
        $this->resultLayoutFactory = $resultLayoutFactory;
    }

     /**
     * @return bool
     */
    public function _isAllowed()
    {
        return $this->_authorization->isAllowed('Digital_Projects::manager');
    }
    

    /**
     * Save action
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {

        $resultLayout = $this->resultLayoutFactory->create();
        $resultLayout->getLayout()->getBlock('projects.edit.tab.product')
                     ->setInProduct($this->getRequest()->getPost('index_products', null));

        return $resultLayout;
    }
}
