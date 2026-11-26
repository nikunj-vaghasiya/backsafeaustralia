<?php
namespace Digital\Testimonials\Controller\Adminhtml\testimonials;

use Magento\Backend\App\Action;
use Magento\Framework\App\Filesystem\DirectoryList;

class Save extends \Magento\Backend\App\Action
{

    /**
     * @param Action\Context $context
     */
    public function __construct(Action\Context $context)
    {
        parent::__construct($context);
    }

    /**
     * Save action
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        $data = $this->getRequest()->getPostValue();

        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();
        if ($data) {
            $model = $this->_objectManager->create(\Digital\Testimonials\Model\Testimonials::class);

            $id = $this->getRequest()->getParam('testimonial_id');
            if ($id) {
                $model->load($id);
                $model->setCreatedAt(date('Y-m-d H:i:s'));
            }
            try {
                $uploader = $this->_objectManager->create(
                    \Magento\MediaStorage\Model\File\Uploader::class,
                    ['fileId' => 'image']
                );
                $uploader->setAllowedExtensions(['jpg', 'jpeg', 'gif', 'png']);
                /** @var \Magento\Framework\Image\Adapter\AdapterInterface $imageAdapter */
                $imageAdapter = $this->_objectManager->get(\Magento\Framework\Image\AdapterFactory::class)->create();
                $uploader->setAllowRenameFiles(true);
                $uploader->setFilesDispersion(true);
                /** @var \Magento\Framework\Filesystem\Directory\Read $mediaDirectory */
                $mediaDirectory = $this->_objectManager->get(\Magento\Framework\Filesystem::class)
                    ->getDirectoryRead(DirectoryList::MEDIA);
                $result = $uploader->save($mediaDirectory->getAbsolutePath('emizen_banner'));
                if ($result['error']==0) {
                    $data['image'] = 'emizen_banner' . $result['file'];
                }
            } catch (\Exception $e) {
                
            }
            
            if (isset($data['image']['delete']) && $data['image']['delete'] == '1') {
                $data['image'] = '';
            }
            //print_r($data);die;
            $model->setData($data);

            try {
                //print_r($model);die;
                $model->save();
                $this->messageManager->addSuccess(__('The Testimonials has been saved.'));
                $this->_objectManager->get(\Magento\Backend\Model\Session::class)->setFormData(false);
                if ($this->getRequest()->getParam('back')) {
                    return $resultRedirect->setPath(
                        '*/*/edit',
                        ['testimonial_id' => $model->getId(), '_current' => true]
                    );
                }
                return $resultRedirect->setPath('*/*/');
            } catch (\Magento\Framework\Exception\LocalizedException $e) {
                $this->messageManager->addError($e->getMessage());
            } catch (\RuntimeException $e) {
                $this->messageManager->addError($e->getMessage());
            } catch (\Exception $e) {
                $this->messageManager->addException(
                    $e,
                    __('Something went wrong while saving the Testimonials.')
                );
            }

            $this->_getSession()->setFormData($data);
            return $resultRedirect->setPath(
                '*/*/edit',
                ['testimonial_id' => $this->getRequest()->getParam('testimonial_id')]
            );
        }
        return $resultRedirect->setPath('*/*/');
    }
}
