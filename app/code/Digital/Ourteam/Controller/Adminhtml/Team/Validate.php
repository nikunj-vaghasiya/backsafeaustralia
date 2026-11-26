<?php
namespace Digital\Ourteam\Controller\Adminhtml\Team;

use Digital\Ourteam\Api\Data\OurteamInterface;
use Digital\Ourteam\Controller\Adminhtml\AbstractTeam;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\DataObject;
use Magento\Framework\Exception\LocalizedException;

class Validate extends AbstractTeam
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
            $images = $this->getRequest()->getParam(OurteamInterface::IMAGE_DESKTOP);
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
