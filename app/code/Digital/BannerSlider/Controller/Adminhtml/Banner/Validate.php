<?php
namespace Digital\BannerSlider\Controller\Adminhtml\Banner;

use Digital\BannerSlider\Api\Data\BannerInterface;
use Digital\BannerSlider\Controller\Adminhtml\AbstractBanner;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\DataObject;
use Magento\Framework\Exception\LocalizedException;

class Validate extends AbstractBanner
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
            $images = $this->getRequest()->getParam(BannerInterface::IMAGE_DESKTOP);
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
