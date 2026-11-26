<?php
namespace Digital\Projects\Controller\Adminhtml\Image;

use Digital\Projects\Model\ImageUploader;
use Magento\Backend\App\Action as BackendAction;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Controller\ResultFactory;

class Upload extends BackendAction
{

    /**
     *  Check admin permissions for this controller
     *
     * @see _isAllowed()
     */
    /* @codingStandardsIgnoreStart */
    const ADMIN_RESOURCE = 'Digital_Projects::projects';
    /* @codingStandardsIgnoreEnd */

    /**
     * @var ImageUploader
     */
    protected $imageUploader;

    /**
     * @var string
     */
    protected $fileId;

    /**
     * Upload constructor.
     *
     * @param Context       $context
     * @param ImageUploader $documentUploader
     */
    public function __construct(
        Context $context,
        ImageUploader $documentUploader
    ) {
        parent::__construct($context);
        $this->imageUploader = $documentUploader;
    }

    /**
     * Upload file controller action
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        try {
            $fileId = $this->getRequest()->getParam('param_name');
            $result = $this->imageUploader->saveFileToTmpDir($fileId);
            $result['cookie'] = [
                'name' => $this->_getSession()->getName(),
                'value' => $this->_getSession()->getSessionId(),
                'lifetime' => $this->_getSession()->getCookieLifetime(),
                'path' => $this->_getSession()->getCookiePath(),
                'domain' => $this->_getSession()->getCookieDomain(),
            ];
        } catch (\Exception $e) {
            $result = [
                'error' => $e->getMessage(),
                'errorcode' => $e->getCode(),
            ];
        }

        /** @var \Magento\Framework\Controller\Result\Json $jsonResult */
        $jsonResult = $this->resultFactory->create(ResultFactory::TYPE_JSON);

        return $jsonResult->setData($result);
    }
}
