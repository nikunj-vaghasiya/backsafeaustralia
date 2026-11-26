<?php
namespace Digital\Projects\Controller\Adminhtml\Projects;

use Digital\Projects\Api\Data\ProjectsInterfaceFactory;
use Digital\Projects\Controller\Adminhtml\AbstractProjects;
use Digital\Projects\Model\ResourceModel\Projects\CollectionFactory as ProjectsCollectionFactory;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Registry;
use Magento\Ui\Component\MassAction\Filter;
use Digital\Projects\Model\ResourceModel\Products\CollectionFactory;

class MassStatus extends AbstractProjects
{

    /**
     * @var CollectionFactory
     */
    private $projectsCollectionFactory;

    /**
     * @var Filter
     */
    protected $filter;

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
        CollectionFactory $collectionFactory,
        ProjectsInterfaceFactory $projectsFactory,
        Filter $filter,
        ProjectsCollectionFactory $projectsCollectionFactory
    ) {
        parent::__construct($context, $coreRegistry, $collectionFactory, $projectsFactory, $filter);
        $this->projectsCollectionFactory = $projectsCollectionFactory;
    }

    /**
     * Execute
     *
     * @return \Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\ResultInterface
     * @throws LocalizedException
     */
    public function execute()
    {
        $collections = $this->filter->getCollection($this->projectsCollectionFactory->create());
        $status = $this->getRequest()->getParam('status');
        $totals = 0;
        try {
            /** @var \Digital\Projects\Model\Projects $item */
            foreach ($collections as $item) {
                $item->setStatus($status);
                $item->save();
                $totals++;
            }
            $this->messageManager->addSuccessMessage(__('A total of %1 record(s) have been updated.', $totals));
        } catch (\Exception $e) {
            $this->messageManager->addErrorMessage(__('Something went wrong while update the projects(s).'));
        }

        /** @var \Magento\Framework\Controller\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
        return $resultRedirect->setPath('*/*/');
    }
}
