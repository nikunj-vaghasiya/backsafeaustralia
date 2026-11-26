<?php
namespace Digital\Projects\Observer;

use Digital\Projects\Api\Data\ProjectsInterface;
use Digital\Projects\Helper\RedundantImageChecker;
use Digital\Projects\Model\ImageField;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;

/**
 * @event digital_projects_delete_commit_after
 * @event digital_projects_save_commit_after
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
        $projects = $observer->getEvent()->getData('projects');
        if ($projects && $projects instanceof ProjectsInterface) {
            foreach (ImageField::getField() as $field) {
                $this->redundantImageChecker->process($projects->getOrigData($field));
            }
        }
    }
}
