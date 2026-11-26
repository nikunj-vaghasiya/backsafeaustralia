<?php
namespace Digital\Homeboxes\Controller\Adminhtml\Boxes;

use Digital\Homeboxes\Api\Data\BoxesInterfaceFactory;
use Digital\Homeboxes\Controller\Adminhtml\AbstractBoxes;
use Digital\Homeboxes\Model\ResourceModel\Boxes\CollectionFactory;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Registry;
use Magento\Ui\Component\MassAction\Filter;

class MassStatus extends AbstractBoxes
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
     * @param BoxesInterfaceFactory  $boxesFactory
     * @param CollectionFactory      $collectionFactory
     * @param Filter                 $filter
     */
    public function __construct(
        Context $context,
        Registry $coreRegistry,
        BoxesInterfaceFactory $boxesFactory,
        CollectionFactory $collectionFactory,
        Filter $filter
    ) {
        parent::__construct($context, $coreRegistry, $boxesFactory);
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
            /** @var \Digital\Homeboxes\Model\Boxes $item */
            foreach ($collections as $item) {
                $item->setStatus($status);
                $item->save();
                $totals++;
            }
            $this->messageManager->addSuccessMessage(__('A total of %1 record(s) have been updated.', $totals));
        } catch (\Exception $e) {
            $this->messageManager->addErrorMessage(__('Something went wrong while update the boxes(s).'));
        }

        /** @var \Magento\Framework\Controller\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
        return $resultRedirect->setPath('*/*/');
    }
}
