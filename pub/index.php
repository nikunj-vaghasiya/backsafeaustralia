<?php
/**
 * Public alias for the application entry point
 *
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
use Magento\Framework\App\Bootstrap;

ini_set('display_errors',1);
try {
    /*if(!in_array($_SERVER['REMOTE_ADDR'], ['202.131.115.225','202.131.115.226','202.131.115.227','202.131.115.228','202.131.115.229','202.131.115.230','103.254.244.234','103.254.244.235','103.254.244.236','103.254.244.237','103.254.244.238','220.245.216.146','218.214.236.227','27.33.248.57', '203.132.81.112', '203.121.205.133', '162.120.184.49'])
    ) {
        require __DIR__ . '/landing/index.html';
        exit(1);
    }*/
    require __DIR__ . '/../app/bootstrap.php';
} catch (\Exception $e) {
    echo <<<HTML
<div style="font:12px/1.35em arial, helvetica, sans-serif;">
    <div style="margin:0 0 25px 0; border-bottom:1px solid #ccc;">
        <h3 style="margin:0;font-size:1.7em;font-weight:normal;text-transform:none;text-align:left;color:#2f2f2f;">
        Autoload error</h3>
    </div>
    <p>{$e->getMessage()}</p>
</div>
HTML;
    http_response_code(500);
    exit(1);
}

$bootstrap = Bootstrap::create(BP, $_SERVER);
/** @var \Magento\Framework\App\Http $app */
$app = $bootstrap->createApplication(\Magento\Framework\App\Http::class);
$bootstrap->run($app);
