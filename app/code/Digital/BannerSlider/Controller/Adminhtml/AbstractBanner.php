<?php
namespace Digital\BannerSlider\Controller\Adminhtml;

use Digital\BannerSlider\Api\Data\BannerInterfaceFactory;
use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Registry;

/**
 * Class AbstractBanner
 *
 * @package Digital\BannerSlider\Controller\Adminhtml
 */
abstract class AbstractBanner extends Action
{
    /**
     * Authorization level of a basic admin session
     *
     * @see _isAllowed()
     */
    /* @codingStandardsIgnoreStart */
    const ADMIN_RESOURCE = 'Digital_BannerSlider::banner';
    /* @codingStandardsIgnoreEnd */

    /**
     * Get Coreregistry
     *
     * @var Registry
     */
    protected $_coreRegistry = null;

    /**
     * @var BannerInterfaceFactory
     */
    protected $bannerFactory;

    /**
     * AbstractBanner constructor.
     *
     * @param Context                $context
     * @param Registry               $coreRegistry
     * @param BannerInterfaceFactory $bannerFactory
     */
    public function __construct(
        Context $context,
        Registry $coreRegistry,
        BannerInterfaceFactory $bannerFactory
    ) {
        parent::__construct($context);
        $this->_coreRegistry = $coreRegistry;
        $this->bannerFactory = $bannerFactory;
    }

    /**
     * Init action
     *
     * @return $this
     */

    /* @codingStandardsIgnoreStart */
    protected function _initAction()
    {
        $this->_view->loadLayout();
        $this->_setActiveMenu(
            'Digital_BannerSlider::manager'
        )->_addBreadcrumb(
            __('Manage Banner'),
            __('Manage Banner')
        );
        return $this;
    }
    /* @codingStandardsIgnoreEnd */
}
