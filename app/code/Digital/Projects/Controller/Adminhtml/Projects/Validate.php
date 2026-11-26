<?php
namespace Digital\Projects\Controller\Adminhtml\Projects;

use Digital\Projects\Api\Data\ProjectsInterface;
use Digital\Projects\Controller\Adminhtml\AbstractProjects;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\DataObject;
use Magento\Framework\Exception\LocalizedException;

class Validate extends AbstractProjects
{
    /**
     * Execute
     *
     * @return \Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\Result\Json|\Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        /** @var \Magento\Framework\Controller\Result\Json $resultJson */
        $resultJson = $this->resultFactory->create(ResultFactory::TYPE_JSON);
        $resultJson->setData($this->validateData());

        return $resultJson;
    }

    /**
     * Validate Data
     *
     * @return DataObject
     */
    private function validateData()
    {
        $error = false;
        $messages = [];
        $response = new DataObject();
        try {
            $images = $this->getRequest()->getParam(ProjectsInterface::IMAGE_LISTING);
            if (!($images && is_array($images))) {
                throw new LocalizedException(__('You should upload image listing.'));
            }
        } catch (LocalizedException $e) {
            $error = true;
            $messages[] = $e->getMessage();
        }
        $result = [
            'error' => $error,
            'messages' => $messages,
        ];
        $response->setData($result);

        return $response;
    }
}
