<?php
try {
    include 'object_class.php';

    $websiteId = 1;

    define('DORMANT_SHEDULER_LOG_DIR', 'massdormant/');

    /* KEY is the respective job code of cron and numeric value is DORMANT TIMEOUT in minutes */
    $eligibleJobCodeAndTimeoutList = [
        'integration_import_specific_customer_pricing' => 3,
        'integration_import_customer_pricing' => 5,
        'integration_order_another_request_cron' => 15,
    ];

    $scheduleResourceModel = $GLOBALS['objectManager']->get('\Magento\Cron\Model\ResourceModel\Schedule');
    $scheduleFactory = $GLOBALS['objectManager']->get('\Magento\Cron\Model\ScheduleFactory');
    $manualHelper = $GLOBALS['objectManager']->get('\Integration\ManualSync\Helper\Data');
    $apiCustomerHelper = $GLOBALS['objectManager']->get('\Integration\Customer\Helper\Data');

    /*$apiCustomerHelper->createLogFiles("Run", 'INFO', DORMANT_SHEDULER_LOG_DIR, 'success.log',$websiteId);*/

    $runningJobs = $scheduleFactory->create()->getCollection();
    $runningJobs->addFieldToFilter('status', \Magento\Cron\Model\Schedule::STATUS_RUNNING);
    $runningJobs->addFieldToFilter('job_code', ['in' => array_keys($eligibleJobCodeAndTimeoutList)]);

    if ($runningJobs && $runningJobs->getSize()) {
        foreach ($runningJobs as $schedule) {
            if (!is_null($schedule->getExecutedAt()) && is_null($schedule->getFinishedAt())) {
                $currentTime = $manualHelper->getCurrentDatetime();
                $timeDiff = _getDateDifference($schedule->getExecutedAt(), $currentTime);
                if ($timeDiff && $timeDiff >= $eligibleJobCodeAndTimeoutList[trim($schedule->getJobCode())]) {
                    $newMessage = 'Set missed status in running job by dormant scheduler';
                    $exMessage = $schedule->getMessages();
                    if (!is_null($exMessage) && is_string($exMessage)) {
                        $newMessage .= " - Actual Job code failed error: " . $exMessage;
                    }

                    $schedule->setStatus(\Magento\Cron\Model\Schedule::STATUS_MISSED)
                        ->setMessages($newMessage)
                        ->save();
                    /* @codingStandardsIgnoreStart */
                    $apiCustomerHelper->createLogFiles("Missed Job - Schedule ID: " . $schedule->getId() . " - Job code: " . $schedule->getJobCode() . " - Created At: " . $schedule->getCreatedAt() . " - Scheduled At:" . $schedule->getScheduledAt() . " - Executed At:" . $schedule->getExecutedAt() . " - Missed/Dormant At: " . $currentTime . " - Time Diff in Mins: " . $timeDiff, 'INFO', DORMANT_SHEDULER_LOG_DIR, 'success.log', $websiteId);
                    /* @codingStandardsIgnoreStart */
                }
            } else {
                /* @codingStandardsIgnoreStart */
                $apiCustomerHelper->createLogFiles("Cron Job - Schedule ID: " . $schedule->getId() . " - Job code: " . $schedule->getJobCode() . " - Created At: " . $schedule->getCreatedAt() . " - Scheduled At:" . $schedule->getScheduledAt() . " - Executed At:" . $schedule->getExecutedAt(), 'INFO', DORMANT_SHEDULER_LOG_DIR, 'error.log', $websiteId);
                /* @codingStandardsIgnoreStart */
            }
        }
    }
} catch (Exception $e) {
    $apiCustomerHelper->createLogFiles("Dormant scheduler error: " . $e->getMessage(),
        'INFO', DORMANT_SHEDULER_LOG_DIR,
        'error.log', $websiteId);
}

function _getDateDifference($startDate, $currentDate)
{
    $websiteId = 1;

    try {
        if ($startDate && $currentDate) {
            $startTimeStr = strtotime($startDate);
            $currentTimeStr = strtotime($currentDate);
            $maxTime = max($startTimeStr, $currentTimeStr);
            $minTime = min($startTimeStr, $currentTimeStr);

            return (int) (($maxTime - $minTime) / 60);
        }

        return false;
    } catch (\Exception $e) {
        $apiCustomerHelper->createLogFiles("Dormant scheduler date difference error: " . $e->getMessage(),
            'INFO',
            DORMANT_SHEDULER_LOG_DIR,
            'error.log',
            $websiteId);
        return false;
    }
}
