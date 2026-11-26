<?php

namespace Digital\BannerSlider\Controller\Adminhtml\Banner;

use Digital\BannerSlider\Controller\Adminhtml\AbstractBanner;
use Digital\BannerSlider\Model\ImageField;

class Edit extends AbstractBanner
{

    /**
     * Execute
     *
     * @return void
     */

    /* @codingStandardsIgnoreStart */
    public function execute()
    {
        /** @var \Digital\BannerSlider\Model\Banner $model */
        $model = $this->bannerFactory->create();
        $this->_coreRegistry->register('current_banner', $model);

        $id = $this->getRequest()->getParam('id');
        if ($id) {
            $model->load($id);
            if (!$model->getId()) {
                $this->messageManager->addErrorMessage(__('This banner no longer exists.'));
                $this->_redirect('bannerslider/banner/*');
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
            $id ? __('Edit Banner') : __('New Banner'),
            $id ? __('Edit Banner') : __('New Banner')
        );

        $this->_view->getPage()->getConfig()->getTitle()->prepend(
            $model->getId() ? $model->getTitle() : __('New Banner')
        );
        $this->_view->renderLayout();
    }
    /* @codingStandardsIgnoreEnd */
}
