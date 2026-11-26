<?php
namespace Digital\Ourteam\Controller\Adminhtml\Team;

use Digital\Ourteam\Controller\Adminhtml\AbstractTeam;
use Magento\Framework\Exception\LocalizedException;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Registry;
use Digital\Ourteam\Api\Data\OurteamInterfaceFactory;
use Digital\Ourteam\Model\ImageUploader;

class Save extends AbstractTeam
{
    /**
     * @var ImageUploader
     */
    protected $imageUploader;

    /**
     * AbstractTeam constructor.
     *
     * @param Context                $context
     * @param Registry               $coreRegistry
     * @param OurteamInterfaceFactory $ourteamFactory
     */
    public function __construct(
        Context $context,
        Registry $coreRegistry,
        OurteamInterfaceFactory $ourteamFactory,
        ImageUploader $documentUploader
    ) {
        parent::__construct($context, $coreRegistry, $ourteamFactory);
        $this->imageUploader = $documentUploader;
    }

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
            /** @var \Digital\Ourteam\Model\Ourteam $model */
            $model = $this->ourteamFactory->create();
            if (!empty($data['team_id'])) {
                $model->load($data['team_id']);
                if (!$model->getId()) {
                    throw new LocalizedException(__('Wrong team Id: %1.', $data['team_id']));
                }
            }
            
            /* image */
            if (isset($data['image'][0]['name']) && isset($data['image'][0]['tmp_name'])) {
                $data['image'] = $data['image'][0]['name'];
                $this->imageUploader->moveFileFromTmp($data['image']);
            } elseif (isset($data['image'][0]['name']) && !isset($data['image'][0]['tmp_name'])) {
                $data['image'] = $data['image'][0]['name'];
            } else {
                $data['image'] = '';
            }
            
            $model->addData($data);
            try {
                $this->_eventManager->dispatch(
                    'controller_action_digital_team_save_entity_before',
                    ['controller' => $this, 'team' => $model]
                );
                $model->save();
                $this->messageManager->addSuccessMessage(__('The team has been saved.'));
                $this->_objectManager->get(\Magento\Backend\Model\Session::class)->setPageData(false);
                if ($this->getRequest()->getParam('back')) {
                    return $resultRedirect->setPath('*/*/edit', ['id' => $model->getId(), '_current' => true]);
                }

                return $resultRedirect->setPath('*/*/');
            } catch (\Exception $e) {
                $this->messageManager->addErrorMessage(__('Something went wrong while saving the team.'));
                $this->_objectManager->get(\Psr\Log\LoggerInterface::class)->critical($e->getMessage());
                if($this->getRequest()->getParam('id')){
                    return $resultRedirect->setPath('*/*/edit', ['id' => $this->getRequest()->getParam('id')]);
                }
                return $resultRedirect->setPath('*/*/new');
            }
        }
        return $resultRedirect->setPath('*/*/');
    }
}