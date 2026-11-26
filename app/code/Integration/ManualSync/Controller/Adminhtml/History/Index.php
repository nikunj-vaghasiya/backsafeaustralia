<?php
namespace Integration\ManualSync\Controller\Adminhtml\History;

use Magento\Backend\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;

class Index extends \Magento\Backend\App\Action
{
    /**
     * @var PageFactory
     */
    protected $manualHelper;
    
    protected $resultPageFactory;

    /**
     * @param Context $context
     * @param PageFactory $resultPageFactory
     */
    public function __construct(
        Context $context,
        PageFactory $resultPageFactory,
        \Integration\ManualSync\Helper\Data $manualHelper
    ) {
        parent::__construct($context);
        $this->resultPageFactory = $resultPageFactory;
        $this->manualHelper = $manualHelper;
    }

    /**
     * Index action
     *
     * @return void
     */
    public function execute()
    {
        /** @var \Magento\Backend\Model\View\Result\Page $resultPage */
        $resultPage = $this->resultPageFactory->create();
        $resultPage->setActiveMenu('Integration_ManualSync::manual');
        $typeId = $this->getRequest()->getParam('type',0);
        $resultPage->getConfig()->getTitle()->prepend(__($this->manualHelper->getTitleTextByTypeId($typeId)));
        return $resultPage;
    }
}
?>
