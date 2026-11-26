<?php
namespace Digital\Ourteam\Observer;

use Digital\Ourteam\Api\Data\OurteamInterface;
use Digital\Ourteam\Helper\RedundantImageChecker;
use Digital\Ourteam\Model\ImageField;
use Digital\Ourteam\Model\ImageUploader;
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
        $team = $observer->getEvent()->getData('ourteam');
        if ($team && $team instanceof OurteamInterface) {
            foreach (ImageField::getField() as $field) {
                $this->processFile($team, $field);
            }
        }
    }

    /**
     * Process File
     *
     * @param \Digital\Ourteam\Model\team|OurteamInterface $object
     * @param key $key
     * @return $this
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function processFile(OurteamInterface $object, $key)
    {
        $files = $object->getData($key);
        $object->setData($key, null);
        if (is_array($files) && !empty($files)) {
            foreach ($files as $file) {
                if (is_array($file) && empty($file['name'])) {
                    continue;
                }
                $name = $file['name'];
                /*Upload New File*/
                if (isset($file['type']) && $file['tmp_name']) {
                    $this->imageUploader->moveFileFromTmp($name);
                }
                $object->setData($key, $name);
            }
        }

        return $this;
    }
}
