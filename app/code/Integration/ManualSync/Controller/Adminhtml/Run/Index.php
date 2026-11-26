<?php
namespace Integration\ManualSync\Controller\Adminhtml\Run;

use Magento\Backend\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;
use Magento\Framework\Controller\ResultFactory;

class Index extends \Magento\Backend\App\Action
{
    protected $resultPageFactory;
    protected $manualSyncWatch;
    protected $manualSyncHelper;

    /**
     * @param Context $context
     * @param PageFactory $resultPageFactory
     */
    public function __construct(
        Context $context,
        PageFactory $resultPageFactory,
        \Integration\ManualSync\Helper\Data $manualSyncHelper,
        \Integration\ManualSync\Model\Watch $manualSyncWatch
    ) {
        parent::__construct($context);
        $this->resultPageFactory = $resultPageFactory;
        $this->manualSyncWatch = $manualSyncWatch;
        $this->manualSyncHelper = $manualSyncHelper;
    }

    /**
     * Index action
     *
     * @return void
     */
    protected function _isAllowed()
    {
     return $this->_authorization->isAllowed('Integration_ManualSync::manual');
    }

    public function execute()
    {
        $data = $this->getRequest()->getPostValue();
        /** @var \Magento\Backend\Model\View\Result\Page $resultRedirect */
        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
        $resultPage = $this->resultPageFactory->create();
        $resultPage->setActiveMenu('Integration_ManualSync::manual');
        $title = $this->manualSyncHelper->getTitleTextByTypeId($this->getRequest()->getParam('type'));
        $resultPage->getConfig()->getTitle()->prepend(__('Manual Sync Manager - '. $title));
        $websiteId = $this->manualSyncHelper->getWebsiteIdByStoreId($this->getRequest()->getParam('store_id'));

        $lastRunningRecord = $this->manualSyncWatch->getLastRunningRecord($this->getRequest()->getParam('type'),\Integration\ManualSync\Model\Status::SYNC_RUNNING_STATUS,$websiteId);

        if($lastRunningRecord && $lastRunningRecord->getId()){
            $resultRedirect->setUrl($this->_redirect->getRefererUrl());
            $this->messageManager->addError(__('One of sync already running. Please wait..'));
            return $resultRedirect;
        }
        return $resultPage;
    }
}?>