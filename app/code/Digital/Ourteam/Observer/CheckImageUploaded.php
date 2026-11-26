<?php

namespace Digital\Ourteam\Observer;

use Digital\Ourteam\Api\Data\OurteamInterface;
use Digital\Ourteam\Helper\RedundantImageChecker;
use Digital\Ourteam\Model\ImageField;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;

/**
 * @event digital_team_delete_commit_after
 * @event digital_team_save_commit_after
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
        $team = $observer->getEvent()->getData('ourteam');
        if ($team && $team instanceof OurteamInterface) {
            foreach (ImageField::getField() as $field) {
                $this->redundantImageChecker->process($team->getOrigData($field));
            }
        }
    }
}
