<?php
namespace Digital\Projects\Controller\Adminhtml;

use Digital\Projects\Api\Data\ProjectsInterfaceFactory;
use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Registry;
use Magento\Ui\Component\MassAction\Filter;

/**
 * Class AbstractProjects
 *
 * @package Digital\Projects\Controller\Adminhtml
 */
abstract class AbstractProjects extends Action
{
    /**
     * Authorization level of a basic admin session
     *
     * @see _isAllowed()
     */
    /* @codingStandardsIgnoreStart */
    const ADMIN_RESOURCE = 'Digital_Projects::projects';
    /* @codingStandardsIgnoreEnd */

    /**
     * Get Coreregistry
     *
     * @var Registry
     */
    protected $_coreRegistry = null;

    /**
     * @var ProjectsInterfaceFactory
     */
    protected $projectsFactory;
    protected $proCollectionFactory;
    protected $filter;

    /**
     * AbstractProjects constructor.
     *
     * @param Context                $context
     * @param Registry               $coreRegistry
     * @param ProjectsInterfaceFactory  $projectsFactory
     */
    public function __construct(
        Context $context,
        Registry $coreRegistry,
        \Digital\Projects\Model\ResourceModel\Products\CollectionFactory $proCollectionFactory,
        ProjectsInterfaceFactory $projectsFactory,
        Filter $filter
    ) {
        parent::__construct($context);
        $this->_coreRegistry = $coreRegistry;
        $this->proCollectionFactory = $proCollectionFactory;
        $this->projectsFactory = $projectsFactory;
        $this->filter = $filter;
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
            'Digital_Projects::manager'
        )->_addBreadcrumb(
            __('Manage Projects'),
            __('Manage Projects')
        );
        return $this;
    }
    /* @codingStandardsIgnoreEnd */
}
