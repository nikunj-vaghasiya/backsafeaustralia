<?php
namespace Digital\Projects\Observer;

use Digital\Projects\Api\Data\ProjectsInterface;
use Digital\Projects\Helper\RedundantImageChecker;
use Digital\Projects\Model\ImageField;
use Digital\Projects\Model\ImageUploader;
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
        $projects = $observer->getEvent()->getData('projects');
        if ($projects && $projects instanceof ProjectsInterface) {
            foreach (ImageField::getField() as $field) {
                $this->processFile($projects, $field);
            }
        }
    }

    /**
     * Process File
     *
     * @param \Digital\Projects\Model\Projects|ProjectsInterface $object
     * @param key $key
     * @return $this
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function processFile(ProjectsInterface $object, $key)
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
