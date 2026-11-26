<?php
namespace Integration\ManualSync\Controller\Adminhtml\Dormant;

use Magento\Backend\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;
use Magento\Framework\Controller\ResultFactory;

class Process extends \Magento\Backend\App\Action
{
    /**
     * @var PageFactory
     */
    protected $resultPageFactory;

    /**
     * @var Buffer
     */
    protected $cronBuffer;

    /**
     * @var Watch
     */
    protected $manualSyncWatch;

    /**
     * @var Data
     */
    protected $manualSyncHelper;

    /**
     * @var View
     */
    protected $manualView;

    /**
     * @param Context $context
     * @param PageFactory $resultPageFactory
     */
    public function __construct(
        Context $context,
        PageFactory $resultPageFactory,
        \Integration\ManualSync\Helper\Data $manualSyncHelper,
        \Integration\ManualSync\Model\Watch $manualSyncWatch,
        \Integration\ManualSync\Block\Adminhtml\View $manualView,
        \Integration\Cron\Model\Buffer $cronBuffer
    ) {
        parent::__construct($context);
        $this->resultPageFactory = $resultPageFactory;
        $this->cronBuffer = $cronBuffer;
        $this->manualSyncWatch = $manualSyncWatch;
        $this->manualSyncHelper = $manualSyncHelper;
        $this->manualView = $manualView;
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
        /** @var \Magento\Backend\Model\View\Result\Page $resultRedirect */
        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
        $resultPage = $this->resultPageFactory->create();
        $resultPage->setActiveMenu('Integration_ManualSync::manual');
        $title = $this->manualSyncHelper->getTitleTextByTypeId($this->getRequest()->getParam('type'));
        $resultPage->getConfig()->getTitle()->prepend(__('Manual Sync Manager - '. $title));
        $websiteId = $this->manualSyncHelper->getWebsiteIdByStoreId($this->getRequest()->getParam('store_id'));
        $msg = 'No running process found';
        $success = false;

        /* Cron buffer table sync running state check and update */

        $cronTypeId = $this->getRequest()->getParam('type');

        switch ($cronTypeId) {
            case $this->manualSyncHelper::CUSTOMER_IMPORT_TYPE_ID:
                $cronTypeId = $this->manualSyncHelper::CUSTOMER_DUMP_TYPE_ID;
            break;
            case $this->manualSyncHelper::NZ_CUSTOMER_IMPORT_TYPE_ID:
                $cronTypeId = $this->manualSyncHelper::NZ_CUSTOMER_DUMP_TYPE_ID;
            break;
            case $this->manualSyncHelper::PRODUCT_IMPORT_TYPE_ID:
                $cronTypeId = $this->manualSyncHelper::PRODUCT_DUMP_TYPE_ID;
            break;
            case $this->manualSyncHelper::NZ_PRODUCT_IMPORT_TYPE_ID:
                $cronTypeId = $this->manualSyncHelper::NZ_PRODUCT_DUMP_TYPE_ID;
            break;
            case $this->manualSyncHelper::CUSTOMER_PRICING_IMPORT_TYPE_ID:
                $cronTypeId = $this->manualSyncHelper::CUSTOMER_PRICING_GUEST_DUMP_TYPE_ID;
            break;
            case $this->manualSyncHelper::NZ_CUSTOMER_PRICING_IMPORT_TYPE_ID:
                $cronTypeId = $this->manualSyncHelper::NZ_CUSTOMER_PRICING_GUEST_DUMP_TYPE_ID;
            break;
            case $this->manualSyncHelper::CUSTOMER_PRICING_IMPORT_TYPE_ID:
                $cronTypeId = $this->manualSyncHelper::CUSTOMER_PRICING_DUMP_TYPE_ID;
            break;
            case $this->manualSyncHelper::NZ_CUSTOMER_DYNAMIC_PRICING_IMPORT_TYPE_ID:
                $cronTypeId = $this->manualSyncHelper::NZ_CUSTOMER_PRICING_DUMP_TYPE_ID;
            break;
        default:
            $cronTypeId;
           break;
        }

        $cronBufferRunningBatch = $this->manualView->checkCronRunningBatch($cronTypeId,$this->getRequest()->getParam('store_id'));

        if($cronBufferRunningBatch && $cronBufferRunningBatch->getId()){
                $this->cronBuffer->setId($cronBufferRunningBatch->getId())
                                ->setNewFlag(0)
                                ->setStatus(\Integration\ManualSync\Model\Status::SYNC_DORMANT_STATUS)
                                ->save();
            $success = true;
        }

        /* Manual sync running state check and update */

        $lastRunningRecord = $this->manualSyncWatch->getLastRunningRecord(
            $this->getRequest()->getParam('type'),
            \Integration\ManualSync\Model\Status::SYNC_RUNNING_STATUS,
            $websiteId
        );

        if($lastRunningRecord && $lastRunningRecord->getId()){
            /*echo "<pre>"; print_r($lastRunningRecord->getData()); die;*/
            $this->manualSyncWatch->setId($lastRunningRecord->getId())
                                ->setStatus(\Integration\ManualSync\Model\Status::SYNC_DORMANT_STATUS)
                                ->save();
            $success = true;
        }

        if($success){
            $this->messageManager->addSuccess(__('Status change to dormant successfully.'));
            $resultRedirect->setUrl($this->_redirect->getRefererUrl());
            return $resultRedirect;
        }
        $this->messageManager->addError(__('No running process found'));
        $resultRedirect->setUrl($this->_redirect->getRefererUrl());
        return $resultRedirect;
    }
}?>