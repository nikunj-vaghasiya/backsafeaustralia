<?php

declare(strict_types=1);

namespace Digital\Cmsbanner\Controller\Adminhtml\Banner;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Catalog\Model\ImageUploader;
use Magento\Framework\App\Action\HttpPostActionInterface;
use Magento\Framework\Controller\ResultFactory;

class Upload extends Action implements HttpPostActionInterface
{
    public const ADMIN_RESOURCE = 'Digital_Cmsbanner::banner_save';

    public function __construct(
        Context $context,
        private readonly ImageUploader $imageUploader
    ) {
        parent::__construct($context);
    }

    public function execute()
    {
        try {
            $fileId = $this->getRequest()->getParam('param_name', 'image');

            $result = $this->imageUploader->saveFileToTmpDir($fileId);

            return $this->resultFactory
                ->create(ResultFactory::TYPE_JSON)
                ->setData($result);

        } catch (\Exception $e) {

            return $this->resultFactory
                ->create(ResultFactory::TYPE_JSON)
                ->setData([
                    'error' => 1,
                    'message' => $e->getMessage()
                ]);
        }
    }
}
