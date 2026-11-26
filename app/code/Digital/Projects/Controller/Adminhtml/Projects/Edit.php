<?php
namespace Digital\Projects\Controller\Adminhtml\Projects;

use Digital\Projects\Controller\Adminhtml\AbstractProjects;
use Digital\Projects\Model\ImageField;

class Edit extends AbstractProjects
{

    /**
     * Execute
     *
     * @return void
     */

    /* @codingStandardsIgnoreStart */
    public function execute()
    {
        /** @var \Digital\Projects\Model\Projects $model */
        $model = $this->projectsFactory->create();
        $this->_coreRegistry->register('current_projects', $model);

        $id = $this->getRequest()->getParam('id');
        if ($id) {
            $model->load($id);
            if (!$model->getId()) {
                $this->messageManager->addErrorMessage(__('This Projects no longer exists.'));
                $this->_redirect('projects/projects/*');
                return;
            }
        }
        $data = $this->_objectManager->get('Magento\Backend\Model\Session')->getPageData(true);
        if (!empty($data)) {
            foreach (ImageField::getField() as $field) {
                unset($data[$field]);
            }
            $model->addData($data);
        }

        $this->_initAction();
        $this->_addBreadcrumb(
            $id ? __('Edit Projects') : __('New Projects'),
            $id ? __('Edit Projects') : __('New Projects')
        );

        $this->_view->getPage()->getConfig()->getTitle()->prepend(
            $model->getId() ? $model->getTitle() : __('New Projects')
        );
        $this->_view->renderLayout();
    }
    /* @codingStandardsIgnoreEnd */
}
