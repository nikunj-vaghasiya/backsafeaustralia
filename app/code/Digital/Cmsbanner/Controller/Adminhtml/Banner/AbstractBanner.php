<?php

declare(strict_types=1);

namespace Digital\Cmsbanner\Controller\Adminhtml\Banner;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\App\Action\HttpGetActionInterface;

abstract class AbstractBanner extends Action
{
    public const ADMIN_RESOURCE = 'Digital_Cmsbanner::banner_list';

    public function __construct(Context $context)
    {
        parent::__construct($context);
    }
}
