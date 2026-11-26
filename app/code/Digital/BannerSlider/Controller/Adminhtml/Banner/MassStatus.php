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

class MassStatus extends AbstractBanner
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
        $status = $this->getRequest()->getParam('status');
        $totals = 0;
        try {
            /** @var \Digital\BannerSlider\Model\Banner $item */
            foreach ($collections as $item) {
                $item->setStatus($status);
                $item->save();
                $totals++;
            }
            $this->messageManager->addSuccessMessage(__('A total of %1 record(s) have been updated.', $totals));
        } catch (\Exception $e) {
            $this->messageManager->addErrorMessage(__('Something went wrong while update the banner(s).'));
        }

        /** @var \Magento\Framework\Controller\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
        return $resultRedirect->setPath('*/*/');
    }
}
