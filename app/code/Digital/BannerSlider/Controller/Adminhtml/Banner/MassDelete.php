<?php

namespace Digital\BannerSlider\Controller\Adminhtml\Banner;

use Digital\BannerSlider\Api\Data\BannerInterfaceFactory;
use Digital\BannerSlider\Controller\Adminhtml\AbstractBanner;
use Digital\BannerSlider\Model\ResourceModel\Banner\CollectionFactory;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Registry;
use Magento\Ui\Component\MassAction\Filter;

class MassDelete extends AbstractBanner
{

    /**
     * @var CollectionFactory
     */
    private $collectionFactory;

    /**
     * @var Filter
     */
    private $filter;

    /**
     * MassDelete constructor.
     *
     * @param Context                $context
     * @param Registry               $coreRegistry
     * @param BannerInterfaceFactory $bannerFactory
     * @param CollectionFactory      $collectionFactory
     * @param Filter                 $filter
     */
    public function __construct(
        Context $context,
        Registry $coreRegistry,
        BannerInterfaceFactory $bannerFactory,
        CollectionFactory $collectionFactory,
        Filter $filter
    ) {
        parent::__construct($context, $coreRegistry, $bannerFactory);
        $this->collectionFactory = $collectionFactory;
        $this->filter = $filter;
    }

    /**
     * Execute
     *
     * @return \Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\ResultInterface
     * @throws LocalizedException
     */
    public function execute()
    {
        $collections = $this->filter->getCollection($this->collectionFactory->create());
        $totals = 0;
        try {
            /** @var \Digital\BannerSlider\Model\Banner $item */
            foreach ($collections as $item) {
                $item->delete();
                $totals++;
            }
            $this->messageManager->addSuccessMessage(__('A total of %1 record(s) have been deleted.', $totals));
        } catch (\Exception $e) {
            $this->messageManager->addErrorMessage(__('Something went wrong while delete the banner(s).'));
        }

        /** @var \Magento\Framework\Controller\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
        return $resultRedirect->setPath('*/*/');
    }
}
