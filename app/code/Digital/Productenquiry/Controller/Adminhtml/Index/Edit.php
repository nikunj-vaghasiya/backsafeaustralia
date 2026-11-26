<?php
namespace Digital\Productenquiry\Controller\Adminhtml\Index;

use Magento\Backend\App\Action;

class Edit extends \Magento\Backend\App\Action
{
    /**
     * Core registry
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
    )
    {
        $this->resultPageFactory = $resultPageFactory;
        $this->_coreRegistry = $registry;
        parent::__construct($context);
    }

    /*
     * @inheritdoc
     */

    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Digital_Productenquiry::save');
    }

    /**
     * Init actions
     *
     * @return $this
     */

    protected function _initAction()
    {
        /** @var \Magento\Backend\Model\View\Result\Page $resultPage */

        $resultPage = $this->resultPageFactory->create();

        $resultPage->setActiveMenu(
            'Digital_Productenquiry::productenquiry'
        )->addBreadcrumb(
            __('Productenquiry'),
            __('Productenquiry')
        )->addBreadcrumb(
            __('Manage Request Quote'),
            __('Manage Request Quote')
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
        $id = $this->getRequest()->getParam('enquiry_id');

        $model = $this->_objectManager->create(\Digital\Productenquiry\Model\Productenquiry::class);

        if ($id) {

            $model->load($id);

            if (!$model->getId()) {

                $this->messageManager->addError(__('This request quote no longer exists.'));

                /** \Magento\Backend\Model\View\Result\Redirect $resultRedirect */

                $resultRedirect = $this->resultRedirectFactory->create();

                return $resultRedirect->setPath('*/*/');

            }

        }

        $data = $this->_objectManager->get(\Magento\Backend\Model\Session::class)->getFormData(true);

        if (!empty($data)) {

            $model->setData($data);

        }

        $this->_coreRegistry->register('productenquiry', $model);

        /** @var \Magento\Backend\Model\View\Result\Page $resultPage */

        $resultPage = $this->_initAction();

        $resultPage->addBreadcrumb(
            $id ? __('View Request Quote') : __('New Request Quote'),
            $id ? __('View Request Quote') : __('New Request Quote')
        );

        $resultPage->getConfig()->getTitle()->prepend($id ? __('View Request Quote') : __('New Productenquiry'));

        return $resultPage;
    }
}
