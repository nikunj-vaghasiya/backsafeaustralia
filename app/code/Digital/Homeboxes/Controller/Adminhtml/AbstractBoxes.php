<?php

namespace Digital\Homeboxes\Controller\Adminhtml;

use Digital\Homeboxes\Api\Data\BoxesInterfaceFactory;
use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Registry;

/**
 * Class AbstractBoxes
 *
 * @package Digital\Homeboxes\Controller\Adminhtml
 */
abstract class AbstractBoxes extends Action
{
    /**
     * Authorization level of a basic admin session
     *
     * @see _isAllowed()
     */
    /* @codingStandardsIgnoreStart */
    const ADMIN_RESOURCE = 'Digital_Homeboxes::boxes';
    /* @codingStandardsIgnoreEnd */

    /**
     * Get Coreregistry
     *
     * @var Registry
     */
    protected $_coreRegistry = null;

    /**
     * @var BoxesInterfaceFactory
     */
    protected $boxesFactory;

    /**
     * AbstractBoxes constructor.
     *
     * @param Context                $context
     * @param Registry               $coreRegistry
     * @param BoxesInterfaceFactory  $boxesFactory
     */
    public function __construct(
        Context $context,
        Registry $coreRegistry,
        BoxesInterfaceFactory $boxesFactory
    ) {
        parent::__construct($context);
        $this->_coreRegistry = $coreRegistry;
        $this->boxesFactory = $boxesFactory;
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
            'Digital_Homeboxes::manager'
        )->_addBreadcrumb(
            __('Manage Boxes'),
            __('Manage Boxes')
        );
        return $this;
    }
    /* @codingStandardsIgnoreEnd */
}
