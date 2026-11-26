<?php

namespace Digital\BannerSlider\Observer;

use Digital\BannerSlider\Api\Data\BannerInterface;
use Digital\BannerSlider\Helper\RedundantImageChecker;
use Digital\BannerSlider\Model\ImageField;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;

/**
 * @event digital_banner_delete_commit_after
 * @event digital_banner_save_commit_after
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
        $banner = $observer->getEvent()->getData('banner');
        if ($banner && $banner instanceof BannerInterface) {
            foreach (ImageField::getField() as $field) {
                $this->redundantImageChecker->process($banner->getOrigData($field));
            }
        }
    }
}
