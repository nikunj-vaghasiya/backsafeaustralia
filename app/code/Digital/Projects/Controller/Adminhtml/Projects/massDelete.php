<?php
namespace Digital\Projects\Controller\Adminhtml\Projects;

use Digital\Projects\Api\Data\ProjectsInterfaceFactory;
use Digital\Projects\Controller\Adminhtml\AbstractProjects;
use Digital\Projects\Model\ResourceModel\Projects\CollectionFactory;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Registry;
use Magento\Ui\Component\MassAction\Filter;

class MassDelete extends \Magento\Backend\App\Action
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
     * @param ProjectsInterfaceFactory  $projectsFactory
     * @param CollectionFactory      $collectionFactory
     * @param Filter                 $filter
     */
    public function __construct(
        Context $context,
        Registry $coreRegistry,
        ProjectsInterfaceFactory $projectsFactory,
        CollectionFactory $collectionFactory,
        Filter $filter
    ) {
        parent::__construct($context, $coreRegistry, $projectsFactory);
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
            /** @var \Digital\Projects\Model\Projects $item */
            foreach ($collections as $item) {
                $item->delete();
                $totals++;
            }
            $this->messageManager->addSuccessMessage(__('A total of %1 record(s) have been deleted.', $totals));
        } catch (\Exception $e) {
            $this->messageManager->addErrorMessage(__('Something went wrong while delete the projects(s).'));
        }

        /** @var \Magento\Framework\Controller\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
        return $resultRedirect->setPath('*/*/');
    }
}
