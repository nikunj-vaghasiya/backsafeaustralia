<?php
namespace Digital\Homeboxes\Controller\Adminhtml\Boxes;

use Digital\Homeboxes\Controller\Adminhtml\AbstractBoxes;
use Digital\Homeboxes\Model\ImageField;

class Edit extends AbstractBoxes
{

    /**
     * Execute
     *
     * @return void
     */

    /* @codingStandardsIgnoreStart */
    public function execute()
    {
        /** @var \Digital\Homeboxes\Model\Boxes $model */
        $model = $this->boxesFactory->create();
        $this->_coreRegistry->register('current_boxes', $model);

        $id = $this->getRequest()->getParam('id');
        if ($id) {
            $model->load($id);
            if (!$model->getId()) {
                $this->messageManager->addErrorMessage(__('This boxes no longer exists.'));
                $this->_redirect('homeboxes/boxes/*');
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
            $id ? __('Edit Boxes') : __('New Boxes'),
            $id ? __('Edit Boxes') : __('New Boxes')
        );

        $this->_view->getPage()->getConfig()->getTitle()->prepend(
            $model->getId() ? $model->getTitle() : __('New Boxes')
        );
        $this->_view->renderLayout();
    }
    /* @codingStandardsIgnoreEnd */
}
