<?php
namespace Digital\Projects\Controller\Adminhtml\Projects;

use Digital\Projects\Controller\Adminhtml\AbstractProjects;
use Magento\Framework\Exception\LocalizedException;

class Save extends AbstractProjects
{

    /**
     * Execute
     *
     * @return $this|\Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\ResultInterface
     * @throws LocalizedException
     */
    public function execute()
    {
        $data = $this->getRequest()->getPostValue();

        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();
        if ($data) {
            /** @var \Digital\Projects\Model\Projects $model */
            $model = $this->projectsFactory->create();
            $rowData = $this->projectsFactory->create();
            if (!empty($data['projects_id'])) {
                $model->load($data['projects_id']);
                if (!$model->getId()) {
                    throw new LocalizedException(__('Wrong Projects Id: %1.', $data['Projects_id']));
                }
            }
            $model->addData($data);
            $this->_objectManager->get(\Magento\Backend\Model\Session::class)->setPageData($model->getData());
            try {
                $this->_eventManager->dispatch(
                    'controller_action_digital_projects_save_entity_before',
                    ['controller' => $this, 'projects' => $model]
                );
                $model->save();
                $this->saveProducts($rowData, $data);
                $this->messageManager->addSuccessMessage(__('The Projects has been saved.'));
                $this->_objectManager->get(\Magento\Backend\Model\Session::class)->setPageData(false);
                if ($this->getRequest()->getParam('back')) {
                    return $resultRedirect->setPath('*/*/edit', ['id' => $model->getId(), '_current' => true]);
                }

                return $resultRedirect->setPath('*/*/');
            } catch (\Exception $e) {
                $this->messageManager->addErrorMessage(__($e->getMessage()));
                $this->_objectManager->get(\Psr\Log\LoggerInterface::class)->critical($e->getMessage());
                return $resultRedirect->setPath('*/*/edit', ['id' => $this->getRequest()->getParam('id')]);
            }
        }
        return $resultRedirect->setPath('*/*/');
    }

    public function saveProducts($model, $post)
    {
        
        if (isset($post['category_products'])) {
            $productIds = json_decode($post['category_products'], true);
            if (isset($productIds['on'])) {
                unset($productIds['on']);
            }
            try {
                $oldProducts = array();
                //$oldProducts = (array) $model->getProducts();
                $Products_coll = $this->proCollectionFactory->create()
                ->addFieldToFilter('projects_id', $post['projects_id'])
                ->addFieldToSelect('product_id')->getData();
                foreach($Products_coll as $prod)
                {
                    $oldProducts[] = $prod['product_id'];
                }

                $newProducts = (array) $productIds;

                $resources = \Magento\Framework\App\ObjectManager::getInstance()->get(\Magento\Framework\App\ResourceConnection::class);
                
                $connection = $resources->getConnection();

                $table = $resources->getTableName(\Digital\Projects\Model\ResourceModel\Projects::TBL_ATT_PRODUCT);

                $insert = array_diff($newProducts, $oldProducts);

                //echo '$insert==>'; print_r($insert);

                $delete = array_diff($oldProducts, $newProducts);

                //echo '$delete==>'; print_r($delete);

                //exit();

                if ($delete) {
                    $where = ['projects_id = ?' => (int) $post['projects_id'], 'product_id IN (?)' => $delete];
                    $connection->delete($table, $where);
                }
                if ($insert) {
                    $data = [];
                    foreach ($insert as $product_id) {
                        $data[] = ['projects_id' => (int) $post['projects_id'], 'product_id' => (int) $product_id];
                    }
                    $connection->insertMultiple($table, $data);
                }
            } catch (\Exception $e) {
                //$this->messageManager->addException($e, __('Something went wrong while saving the selected product.'));
            }
        }
    }
}
