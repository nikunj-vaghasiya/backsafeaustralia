<?php
namespace Digital\Homeboxes\Controller\Adminhtml\Boxes;

use Digital\Homeboxes\Api\Data\BoxesInterface;
use Digital\Homeboxes\Controller\Adminhtml\AbstractBoxes;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\DataObject;
use Magento\Framework\Exception\LocalizedException;

class Validate extends AbstractBoxes
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
            $images = $this->getRequest()->getParam(BoxesInterface::IMAGE_DESKTOP);
            if (!($images && is_array($images))) {
                throw new LocalizedException(__('You should upload image desktop.'));
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
