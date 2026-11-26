<?php
declare(strict_types=1);

namespace Digital\CmsCanonical\Helper;

use Magento\Cms\Model\Page;
use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use Magento\Framework\App\Request\Http;
use Magento\Store\Model\StoreManagerInterface;

class Canonical extends AbstractHelper
{
    /**
     * @var Page
     */
    protected $cmsPage;

    /**
     * @var Http
     */
    protected Http $http;

    /**
     * @var StoreManagerInterface
     */
    protected StoreManagerInterface $_storeManager;

    /**
     * Canonical constructor.
     * @param Context               $context
     * @param Page                  $cmsPage
     * @param Http                  $http
     * @param StoreManagerInterface $storeManager
     */
    public function __construct(
        Context $context,
        Page $cmsPage,
        Http $http,
        StoreManagerInterface $storeManager
    ) {
        $this->cmsPage = $cmsPage;
        $this->http = $http;
        $this->_storeManager = $storeManager;
        parent::__construct($context);
    }

    public function getCanonicalForCustomModules(): string
    {
        if (in_array($this->http->getModuleName(), ['catalog'])) {
            $urlInterface = \Magento\Framework\App\ObjectManager::getInstance()
                ->get(\Magento\Framework\UrlInterface::class);
            $currentUrl = $urlInterface->getCurrentUrl();
            $currentUrl = strtok($currentUrl, '?');
            return $this->createLink($currentUrl);
        }

        return '';
    }

    /**
     * @param string $url
     * @return string
     */
    protected function createLink($url): string
    {
        return '<link rel="canonical" href="' . $url . '" />';
    }
}