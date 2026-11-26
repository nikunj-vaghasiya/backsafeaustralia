<?php
namespace Integration\ManualSync\Block\Adminhtml\History;

class Grid extends \Magento\Backend\Block\Widget\Grid\Extended
{
    /**
     * @var \Magento\Framework\Module\Manager
     */
    protected $moduleManager;

    /**
     * @var \Integration\ManualSync\Model\WatchFactory
     */
    protected $_orderFactory;

    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Backend\Helper\Data $backendHelper
     * @param \Integration\ManualSync\Model\WatchFactory $manualSyncWatchFactory
     * @param \Magento\Framework\Module\Manager $moduleManager
     * @param array $data
     *
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Backend\Helper\Data $backendHelper,
        \Integration\ManualSync\Model\Watch $manualSyncWatchFactory,
        \Magento\Framework\Module\Manager $moduleManager,
        \Integration\ManualSync\Helper\Data $manualHelper,
        array $data = []
    ) {
        $this->_manualSyncWatchFactory = $manualSyncWatchFactory;
        $this->manualHelper = $manualHelper;
        $this->moduleManager = $moduleManager;
        parent::__construct($context, $backendHelper, $data);
    }

    /**
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setId('postGrid');
        $this->setDefaultSort('api_entity_id');
        $this->setDefaultDir('DESC');
        $this->setSaveParametersInSession(true);
        /*$this->setUseAjax(true);
        $this->setVarNameFilter('api_entity_id');*/
    }

    /**
     * @return $this
     */
    protected function _prepareCollection()
    {
        $requestedStoreId = $this->getRequest()->getParam('store_id',null);
        $typeId = $this->getRequest()->getParam('type',false);

        $collection = $this->_manualSyncWatchFactory->getCollection()
                    ->addFieldToFilter('type', array('eq' => $typeId))
                    ->addFieldToFilter('website_id', array('eq' => $this->manualHelper->getWebsiteIdByStoreId($requestedStoreId)))
                    ->setOrder('api_entity_id',"DESC");

        $this->setCollection($collection);

        parent::_prepareCollection();

        return $this;
    }

    /**
     * @return $this
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    protected function _prepareColumns()
    {
        $this->addColumn(
            'entity_id',
            [
                'header' => __('ID'),
                'type' => 'number',
                'index' => 'api_entity_id',
                'header_css_class' => 'col-id',
                'column_css_class' => 'col-id'
            ]
        );

        $this->addColumn(
            'total_number',
            [
                'header' => __('Number of Records'),
                'index' => 'total_number',
                'type' =>'number',
                'header_css_class' => 'col-id',
                'column_css_class' => 'col-id'
            ]
        );

        $this->addColumn(
            'status',
            [
                'header' => __('Status'),
                'index' => 'status',
                'type' => 'options',
                'options' => \Integration\ManualSync\Model\Status::getOptionArray()
            ]
        );

        $this->addColumn(
            'start_at',
            [
                'header' => __('Start At'),
                'index'=> 'start_at',
                'type' => 'datetime',
                'header_css_class' => 'col-date',
                'column_css_class' => 'col-date'
            ]
        );

        $this->addColumn(
            'end_at',
            [
                'header' => __('End At'),
                'index'=> 'end_at',
                'type' => 'datetime',
                'header_css_class' => 'col-date',
                'column_css_class' => 'col-date'
            ]
        );

        $this->addColumn(
            'created_at',
            [
                'header' => __('Created At'),
                'index' => 'created_at',
                'type' => 'datetime',
                'header_css_class' => 'col-date',
                'column_css_class' => 'col-date'
            ]
        );

        $this->addColumn(
            'updated_at',
            [
                'header' => __('Updated At'),
                'index' => 'updated_at',
                'type' => 'datetime',
                'header_css_class' => 'col-date',
                'column_css_class' => 'col-date'
            ]
        );

        $block = $this->getLayout()->getBlock('grid.bottom.links');
        if ($block) {
            $this->setChild('grid.bottom.links', $block);
        }

        return parent::_prepareColumns();
    }

    /**
     * @return string
     */
    public function getGridUrl()
    {
        return $this->getUrl('manualsync/*/index', ['_current' => true]);
    }

    /**
     * @param \Integration\ManualSync\Model\Watch|\Magento\Framework\Object $row
     * @return string
     */
    public function getRowUrl($row)
    {
        return '';
    }
}
