<?php
namespace Digital\Homeboxes\Observer;

use Digital\Homeboxes\Api\Data\BoxesInterface;
use Digital\Homeboxes\Helper\RedundantImageChecker;
use Digital\Homeboxes\Model\ImageField;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;

/**
 * @event digital_boxes_delete_commit_after
 * @event digital_boxes_save_commit_after
 */
class CheckImageUploaded implements ObserverInterface
{
    /**
     * @var RedundantImageChecker
     */
    private $redundantImageChecker;

    /**
     * CheckingImageUploaded constructor.
     *
     * @param RedundantImageChecker $redundantImageChecker
     */
    public function __construct(
        RedundantImageChecker $redundantImageChecker
    ) {
        $this->redundantImageChecker = $redundantImageChecker;
    }

    /**
     * Dispatch event
     *
     * @param Observer $observer
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function execute(Observer $observer)
    {
        $boxes = $observer->getEvent()->getData('boxes');
        if ($boxes && $boxes instanceof BoxesInterface) {
            foreach (ImageField::getField() as $field) {
                $this->redundantImageChecker->process($boxes->getOrigData($field));
            }
        }
    }
}
