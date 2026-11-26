<?php
namespace Digital\Projects\Controller\View;

use Digital\Projects\Helper\Data;
use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;

class Indexprint extends Action
{
    /**
     * @var PageFactory
     */
    protected $resultPageFactory;

    protected $projectsModel;

    protected $helper;

    /**
     * Construct Classes
     *
     * @param Context $context
     * @param \Digital\Projects\Model\Projects $projectsModel
     * @param PageFactory $resultPageFactory
     * @param Data $helper
     */
    public function __construct(
        Context $context,
        \Digital\Projects\Model\Projects $projectsModel,
        PageFactory $resultPageFactory,
        Data $helper
    ) {
        parent::__construct($context);
        $this->projectsModel = $projectsModel;
        $this->resultPageFactory = $resultPageFactory;
        $this->helper = $helper;
    }

    /**
     * Execute Function
     */
    public function execute()
    {
        if (!$this->helper->isEnabled()) {
            $this->_redirect('noRoute');
            return;
        } else {

        $projectsId = $this->getRequest()->getParam('id');
        $projects = $this->projectsModel->load($projectsId);
        $pageFactory = $this->resultPageFactory->create();
        $pageConfig = $pageFactory->getConfig();
        
        /*$pageFactory->getConfig()->getTitle()->set($projects->getTitle());

        $breadcrumbs = $pageFactory->getLayout()->getBlock('breadcrumbs');
        $breadcrumbs->addCrumb(
            'home',
            [
                'label' => __('Home'),
                'title' => __('Home'),
                'link' => $this->_url->getUrl(''),
            ]
        );
        $breadcrumbs->addCrumb(
            'Projects',
            [
                'label' => __('Projects'),
                'title' => __('Projects'),
                'link' => $this->_url->getUrl('projects'),
            ]
        );
        $breadcrumbs->addCrumb(
            'projectsview',
            [
                'label' => $projects->getTitle(),
                'title' => $projects->getTitle(),
            ]
        );*/
        return $pageFactory;

        }
    }
}
