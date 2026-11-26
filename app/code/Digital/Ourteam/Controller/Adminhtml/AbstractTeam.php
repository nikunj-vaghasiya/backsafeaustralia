<?php
namespace Digital\Ourteam\Controller\Adminhtml;

use Digital\Ourteam\Api\Data\OurteamInterfaceFactory;
use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Registry;

/**
 * Class AbstractTeam
 *
 * @package Digital\Ourteam\Controller\Adminhtml
 */
abstract class AbstractTeam extends Action
{
    /**
     * Authorization level of a basic admin session
     *
     * @see _isAllowed()
     */
    /* @codingStandardsIgnoreStart */
    public const ADMIN_RESOURCE = 'Digital_Ourteam::ourteam';
    /* @codingStandardsIgnoreEnd */

    /**
     * Get Coreregistry
     *
     * @var Registry
     */
    protected $_coreRegistry = null;

    /**
     * @var OurteamInterfaceFactory
     */
    protected $ourteamFactory;

    /**
     * AbstractTeam constructor.
     *
     * @param Context                $context
     * @param Registry               $coreRegistry
     * @param OurteamInterfaceFactory $ourteamFactory
     */
    public function __construct(
        Context $context,
        Registry $coreRegistry,
        OurteamInterfaceFactory $ourteamFactory
    ) {
        parent::__construct($context);
        $this->_coreRegistry = $coreRegistry;
        $this->ourteamFactory = $ourteamFactory;
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
            'Digital_Ourteam::manager'
        )->_addBreadcrumb(
            __('Manage Team'),
            __('Manage Team')
        );
        return $this;
    }

    /**
     * Access rights checking
     *
     * @return bool
     */
    protected function _isAllowed()
    {
        return true;
    }
    /* @codingStandardsIgnoreEnd */
}
