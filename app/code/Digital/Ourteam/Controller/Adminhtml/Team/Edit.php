<?php
namespace Digital\Ourteam\Controller\Adminhtml\Team;

use Digital\Ourteam\Controller\Adminhtml\AbstractTeam;
use Digital\Ourteam\Model\ImageField;

class Edit extends AbstractTeam
{
    /**
     * Execute
     *
     * @return void
     */
    /* @codingStandardsIgnoreStart */
    public function execute()
    {
        /** @var \Digital\Ourteam\Model\Ourteam $model */
        $model = $this->ourteamFactory->create();
        $this->_coreRegistry->register('current_team', $model);

        $id = $this->getRequest()->getParam('id');
        if ($id) {
            $model->load($id);
            if (!$model->getTeamId()) {
                $this->messageManager->addErrorMessage(__('This tean no longer exists.'));
                $this->_redirect('ourteam/team/*');
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
            $id ? __('Edit Team') : __('New Team'),
            $id ? __('Edit Team') : __('New Team')
        );

        $this->_view->getPage()->getConfig()->getTitle()->prepend(
            $model->getId() ? $model->getTitle() : __('New Team')
        );
        $this->_view->renderLayout();
    }
    /* @codingStandardsIgnoreEnd */
}
