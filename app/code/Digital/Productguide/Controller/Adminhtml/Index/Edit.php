<?php
namespace Digital\Productguide\Controller\Adminhtml\Index;

use Magento\Backend\App\Action;

class Edit extends \Magento\Backend\App\Action
{

    /**
     * Edit Form Data
     *
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry = null;

    /**
     * @var \Magento\Framework\View\Result\PageFactory
     */

    protected $resultPageFactory;

    /**
     * @param Action\Context $context
     * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
     * @param \Magento\Framework\Registry $registry
     */

    public function __construct(
        Action\Context $context,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Magento\Framework\Registry $registry
    ) {
        $this->resultPageFactory = $resultPageFactory;
        $this->_coreRegistry = $registry;
        parent::__construct($context);
    }

    /**
     * Allow Saving to Users
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Digital_Productguide::manage');
    }

    /**
     * Init actions
     *
     * @return $this
     */

    protected function _initAction()
    {

        // load layout, set active menu and breadcrumbs
        /** @var \Magento\Backend\Model\View\Result\Page $resultPage */
        $resultPage = $this->resultPageFactory->create();
        $resultPage->setActiveMenu(
            'Digital_Productguide::manage'
        )->addBreadcrumb(
            __('Productguide'),
            __('Productguide')
        )->addBreadcrumb(
            __('Manage Productguide'),
            __('Manage Productguide')
        );
        return $resultPage;
    }

    /**
     * Edit CMS page
     *
     * @return void
     */

    public function execute()
    {
        // 1. Get ID and create model
        $id = $this->getRequest()->getParam('productguide_id');
        $model = $this->_objectManager->create(\Digital\Productguide\Model\Productguide::class);
        // 2. Initial checking
        if ($id) {
            $model->load($id);
            if (!$model->getId()) {
                $this->messageManager->addError(__('This Product guide no longer exists.'));
                /** \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
                $resultRedirect = $this->resultRedirectFactory->create();
                return $resultRedirect->setPath('*/*/');
            }
        }
        // 3. Set entered data if was error when we do save
        $data = $this->_objectManager->get(\Magento\Backend\Model\Session::class)->getFormData(true);
        if (!empty($data)) {
            $model->setData($data);
        }
        // 4. Register model to use later in blocks
        $this->_coreRegistry->register('productguide', $model);
        // 5. Build edit form
        /** @var \Magento\Backend\Model\View\Result\Page $resultPage */
        $resultPage = $this->_initAction();
        $resultPage->addBreadcrumb(
            $id ? __('Edit Product Guide') : __('New Product Guide'),
            $id ? __('Edit Product Guide') : __('New Product Guide')
        );

        $resultPage->getConfig()->getTitle()->prepend(__('Manage Product Guide Enquiry'));
        $resultPage->getConfig()->getTitle()->prepend($id ? __('View Product Guide Enquiry') : __('Manage Product Guide'));
        return $resultPage;
    }
}
