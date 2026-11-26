<?php
namespace Integration\ManualSync\Block\Adminhtml\Run;

class Process extends \Magento\Backend\Block\Template
{
    /**
     * @param \Magento\Backend\Block\Widget\Context $context
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Widget\Context $context,
        \Integration\ManualSync\Model\Watch $manualSyncWatch,
        \Integration\ManualSync\Helper\Data $manualSyncHelper,
        /* \Integration\Customer\Model\Dump\Process $customerDump, */
        \Integration\Product\Model\Dump\Process $productDump,
        /* \Integration\Pricing\Model\Dump\Process $customerPricing,
        \Integration\Customer\Model\Batch\Process $customerBatchProcess, */
        \Integration\Product\Model\Batch\Process $productBatchProcess,
        \Integration\Product\Model\Import\Process $productImport,
        /* \Integration\Customer\Model\Import\Process $customerImport,
         \Integration\Pricing\Model\Batch\Process $pricingBatch,
        \Integration\Pricing\Model\Import\Process $productPricingImport,
        \Integration\Pricing\Model\Import\Customer\Process $customerPricingImport,
        \Integration\Customer\Model\Batch\Parent\Process $customerParentBatchProcess,
        \Integration\Customer\Model\Import\Parent\Process $customerParentImport,
        \Integration\Pricing\Model\Dump\Customer\Process $customerDynamicPricing,
        \Integration\Pricing\Model\Batch\Customer\Process $customerPricingBatch,*/
        array $data = []
    ){
        /* $this->customerPricing = $customerPricing; */
        $this->manualSyncWatch = $manualSyncWatch;
        $this->manualSyncHelper = $manualSyncHelper;
        /* $this->customerDump = $customerDump; */
        $this->productDump = $productDump;
        /* $this->customerBatchProcess = $customerBatchProcess; */
        $this->productBatchProcess = $productBatchProcess;
        $this->productImport = $productImport;
        /* $this->customerImport = $customerImport;
        $this->pricingBatch = $pricingBatch;
        $this->productPricingImport = $productPricingImport;
        $this->customerPricingImport = $customerPricingImport;
        $this->customerParentBatchProcess = $customerParentBatchProcess;
        $this->customerParentImport = $customerParentImport;
        $this->customerDynamicPricing = $customerDynamicPricing;
        $this->customerPricingBatch = $customerPricingBatch; */
        parent::__construct($context, $data);
    }

    /**
     * Return store id from param
     *
     * @return int
     */
    public function getStoreId(){
        return $this->getRequest()->getParam('store_id');
    }

    /**
     * Returns entity type id from param
     *
     * @return int
     */
    public function getEntityTypeId(){
        return $this->getRequest()->getParam('type');
    }

    /**
     * Returns price type value from param
     *
     * @return int
     */
    public function getPriceType(){
        return $this->getRequest()->getParam('price_type',0);
    }

    /**
     * Returns price type value from param
     *
     * @return int
     */
    public function getAccountNumber(){
        return $this->getRequest()->getParam('cpricing','');
    }

    /**
     * execute the run process and star dump
     *
     * @return string
     */
    public function runProcess()
    {
        $websiteId = $this->manualSyncHelper->getWebsiteIdByStoreId($this->getStoreId());
        $lastRunningRecord = $this->manualSyncWatch->getLastRunningRecord($this->getEntityTypeId(),\Integration\ManualSync\Model\Status::SYNC_RUNNING_STATUS,$websiteId);
        $mode = $this->manualSyncHelper::MANUAL_MODE;

        if($lastRunningRecord && $lastRunningRecord->getId()){
            return "One of sync already running. Please wait..";
        }

        $this->manualSyncWatch->setType($this->getEntityTypeId())->setStartAt($this->manualSyncHelper->getCurrentDatetime())->setStatus(\Integration\ManualSync\Model\Status::SYNC_RUNNING_STATUS)->setWebsiteId($websiteId)->save();

        /* Manual Dump Execute */
        switch ((int)$this->getEntityTypeId()) {
            /* case \Integration\ManualSync\Helper\Data::CUSTOMER_DUMP_TYPE_ID:
                $this->customerDump
                            ->setWebsiteId($websiteId)
                            ->setMode($mode)
                            ->runProcess();
                break; */
           /*  case \Integration\ManualSync\Helper\Data::CUSTOMER_IMPORT_BATCH_TYPE_ID:
                $this->customerBatchProcess
                                ->setWebsiteId($websiteId)
                                ->setMode($mode)
                                ->setLogTypeId(\Integration\ManualSync\Helper\Data::CUSTOMER_IMPORT_BATCH_TYPE_ID)
                                ->setProcessTypeId(\Integration\ManualSync\Helper\Data::CUSTOMER_DUMP_TYPE_ID)
                                ->runProcess();
                break; */
            /* case \Integration\ManualSync\Helper\Data::CUSTOMER_IMPORT_TYPE_ID:
                $this->customerImport
                                ->setWebsiteId($websiteId)
                                ->setMode($mode)
                                ->setLogTypeId(\Integration\ManualSync\Helper\Data::CUSTOMER_IMPORT_TYPE_ID)
                                ->setProcessTypeId(\Integration\ManualSync\Helper\Data::CUSTOMER_DUMP_TYPE_ID)
                                ->runProcess();
                break; */
           /*  case \Integration\ManualSync\Helper\Data::CUSTOMER_PARENT_ASSIGNMENT_IMPORT_BATCH_TYPE_ID:
                $this->customerParentBatchProcess
                                ->setWebsiteId($websiteId)
                                ->setMode($mode)
                                ->setLogTypeId(\Integration\ManualSync\Helper\Data::CUSTOMER_PARENT_ASSIGNMENT_IMPORT_BATCH_TYPE_ID)
                                ->setProcessTypeId(\Integration\ManualSync\Helper\Data::CUSTOMER_PARENT_ASSIGNMENT_DUMP_TYPE_ID)
                                ->runProcess();
                break; */
            /* case \Integration\ManualSync\Helper\Data::CUSTOMER_PARENT_ASSIGNMENT_IMPORT_TYPE_ID:
                $this->customerParentImport
                                ->setWebsiteId($websiteId)
                                ->setMode($mode)
                                ->setLogTypeId(\Integration\ManualSync\Helper\Data::CUSTOMER_PARENT_ASSIGNMENT_IMPORT_TYPE_ID)
                                ->setProcessTypeId(\Integration\ManualSync\Helper\Data::CUSTOMER_PARENT_ASSIGNMENT_DUMP_TYPE_ID)
                                ->runProcess();
                break; */
            case \Integration\ManualSync\Helper\Data::PRODUCT_DUMP_TYPE_ID:
                $this->productDump
                                ->setWebsiteId($websiteId)
                                ->setMode($mode)
                                ->setLogTypeId(\Integration\ManualSync\Helper\Data::PRODUCT_DUMP_TYPE_ID)
                                ->runProcess();
                break;
            case \Integration\ManualSync\Helper\Data::PRODUCT_IMPORT_BATCH_TYPE_ID:
                $this->productBatchProcess
                                ->setWebsiteId($websiteId)
                                ->setMode($mode)
                                ->setLogTypeId(\Integration\ManualSync\Helper\Data::PRODUCT_IMPORT_BATCH_TYPE_ID)
                                ->setProcessTypeId(\Integration\ManualSync\Helper\Data::PRODUCT_DUMP_TYPE_ID)
                                ->runProcess();
                break;
            case \Integration\ManualSync\Helper\Data::PRODUCT_IMPORT_TYPE_ID:
                $this->productImport
                                ->setWebsiteId($websiteId)
                                ->setMode($mode)
                                ->setLogTypeId(\Integration\ManualSync\Helper\Data::PRODUCT_IMPORT_TYPE_ID)
                                ->setProcessTypeId(\Integration\ManualSync\Helper\Data::PRODUCT_DUMP_TYPE_ID)
                                ->runProcess();
                break;
            /* case \Integration\ManualSync\Helper\Data::CUSTOMER_PRICING_GUEST_DUMP_TYPE_ID:
                $this->customerPricing
                            ->setWebsiteId($websiteId)
                            ->setMode($mode)
                            ->setProcessTypeId(\Integration\ManualSync\Helper\Data::CUSTOMER_PRICING_GUEST_DUMP_TYPE_ID)
                            ->runProcess();
                break; */
            /* case \Integration\ManualSync\Helper\Data::CUSTOMER_PRICING_BATCH_TYPE_ID:
                $this->pricingBatch
                                ->setWebsiteId($websiteId)
                                ->setMode($mode)
                                ->setLogTypeId(\Integration\ManualSync\Helper\Data::CUSTOMER_PRICING_BATCH_TYPE_ID)
                                ->setProcessTypeId(\Integration\ManualSync\Helper\Data::CUSTOMER_PRICING_GUEST_DUMP_TYPE_ID)
                                ->runProcess();
                break; */
           /*  case \Integration\ManualSync\Helper\Data::CUSTOMER_PRICING_IMPORT_TYPE_ID:
                $this->productPricingImport
                                ->setWebsiteId($websiteId)
                                ->setMode($mode)
                                ->setLogTypeId(\Integration\ManualSync\Helper\Data::CUSTOMER_PRICING_IMPORT_TYPE_ID)
                                ->setProcessTypeId(\Integration\ManualSync\Helper\Data::CUSTOMER_PRICING_GUEST_DUMP_TYPE_ID)
                                ->runProcess();
                break; */
            case \Integration\ManualSync\Helper\Data::CUSTOMER_PRICING_DUMP_TYPE_ID:
                $this->customerDynamicPricing
                            ->setWebsiteId($websiteId)
                            ->setMode($mode)
                            ->setPriceType($this->getPriceType())
                            ->setProcessTypeId(\Integration\ManualSync\Helper\Data::CUSTOMER_PRICING_DUMP_TYPE_ID)
                            ->runProcess();
                break;
            case \Integration\ManualSync\Helper\Data::CUSTOMER_DYNAMIC_PRICING_BATCH_TYPE_ID:
                $this->customerPricingBatch
                                ->setWebsiteId($websiteId)
                                ->setMode($mode)
                                ->setAccountNumber($this->getAccountNumber())
                                ->setLogTypeId(\Integration\ManualSync\Helper\Data::CUSTOMER_DYNAMIC_PRICING_BATCH_TYPE_ID)
                                ->setProcessTypeId(\Integration\ManualSync\Helper\Data::CUSTOMER_PRICING_DUMP_TYPE_ID)
                                ->runProcess();
                break;
            /* case \Integration\ManualSync\Helper\Data::CUSTOMER_DYNAMIC_PRICING_IMPORT_TYPE_ID:
                $this->customerPricingImport
                                ->setWebsiteId($websiteId)
                                ->setMode($mode)
                                ->setLogTypeId(\Integration\ManualSync\Helper\Data::CUSTOMER_DYNAMIC_PRICING_IMPORT_TYPE_ID)
                                ->setProcessTypeId(\Integration\ManualSync\Helper\Data::CUSTOMER_PRICING_DUMP_TYPE_ID)
                                ->runProcess();
                break; */
            default:
               echo 'We can not find what you want, sorry';
               break;
        }
        /* Manual Dump Execute */

        if($lastRunningRecord && $lastRunningRecord->getId()){
            $this->manualSyncWatch->setType($this->getEntityTypeId())->setEndAt($this->manualSyncHelper->getCurrentDatetime())->setStatus(\Integration\ManualSync\Model\Status::SYNC_SUCCESS_STATUS)->setWebsiteId($websiteId)->save();
        }
        return $this;
    }
}
