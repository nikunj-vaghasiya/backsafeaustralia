<?php
use Magento\Framework\App\Bootstrap;

require __DIR__ . '/../app/bootstrap.php';

$params = $_SERVER;
$bootstrap = Bootstrap::create(BP, $params);
$objectManager = $bootstrap->getObjectManager();

$state = $objectManager->get(\Magento\Framework\App\State::class);

try {
    $state->setAreaCode('frontend');
} catch (\Exception $e) {
}

$myClass = $objectManager->create(\Digital\Logcleaner\Cron\Logcleaner::class);

$myClass->execute();

echo "Script executed successfully.";