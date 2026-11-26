<?php
namespace Digital\Homeboxes\Observer;

use Digital\Homeboxes\Api\Data\BoxesInterface;
use Digital\Homeboxes\Helper\RedundantImageChecker;
use Digital\Homeboxes\Model\ImageField;
use Digital\Homeboxes\Model\ImageUploader;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;

class ProcessingImageUpload implements ObserverInterface
{
    /**
     * @var ImageUploader
     */
    private $imageUploader;

    /**
     * @var RedundantImageChecker
     */
    private $redundantImageChecker;

    /**
     * ProcessingImageUpload constructor.
     *
     * @param ImageUploader         $imageUploader
     * @param RedundantImageChecker $redundantImageChecker
     */
    public function __construct(
        ImageUploader $imageUploader,
        RedundantImageChecker $redundantImageChecker
    ) {
        $this->imageUploader = $imageUploader;
        $this->redundantImageChecker = $redundantImageChecker;
    }

    /**
     * Execute
     *
     * @param Observer $observer
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function execute(Observer $observer)
    {
        $boxes = $observer->getEvent()->getData('boxes');
        if ($boxes && $boxes instanceof BoxesInterface) {
            foreach (ImageField::getField() as $field) {
                $this->processFile($boxes, $field);
            }
        }
    }

    /**
     * Process File
     *
     * @param \Digital\Homeboxes\Model\Boxes|BoxesInterface $object
     * @param key $key
     * @return $this
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function processFile(BoxesInterface $object, $key)
    {
        $files = $object->getData($key);
        $object->setData($key, null);
        if (is_array($files) && !empty($files)) {
            foreach ($files as $file) {
                if (is_array($file) && empty($file['name'])) {
                    continue;
                }
                $name = $file['name'];
                // Upload New File
                if (isset($file['type']) && $file['tmp_name']) {
                    $this->imageUploader->moveFileFromTmp($name);
                }
                $object->setData($key, $name);
            }
        }

        return $this;
    }
}
