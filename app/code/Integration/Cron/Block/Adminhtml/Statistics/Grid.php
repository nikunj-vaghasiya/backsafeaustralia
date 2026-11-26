<?php
namespace Integration\Cron\Block\Adminhtml\Statistics;

class Grid extends \Magento\Backend\Block\Widget\Grid\Extended
{
    /**
     * @var \Magento\Framework\Module\Manager
     */
    protected $moduleManager;

    /**
     * @var \Sap\ManualSync\Model\WatchFactory
     */
    protected $_orderFactory;

    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Backend\Helper\Data $backendHelper
     * @param \Sap\ManualSync\Model\WatchFactory $manualSyncWatchFactory
     * @param \Magento\Framework\Module\Manager $moduleManager
     * @param array $data
     *
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Backend\Helper\Data $backendHelper,
        \Integration\Cron\Model\ResourceModel\Buffer\CollectionFactory $cronBufferFactory,
        \Integration\ManualSync\Helper\Data $manualHelper,
        array $data = []
    ) {
        $this->_cronBufferFactory = $cronBufferFactory;
        $this->manualHelper = $manualHelper;
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
        $this->setUseAjax(true);
        $this->setSaveParametersInSession(true);
    }

    /**
     * @return $this
     */
    protected function _prepareCollection()
    {
        $requestedStoreId = $this->getRequest()->getParam('store_id') ? $this->getRequest()->getParam('store_id') : 1;
        $websiteId = $this->manualHelper->getWebsiteIdByStoreId($requestedStoreId);
        $collection = $this->_cronBufferFactory->create();
        $collection->addFieldToFilter('status',array('eq' => \Integration\ManualSync\Model\Status::SYNC_DORMANT_STATUS))
            ->addFieldToFilter('website_id', array('eq' => $websiteId));

        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    /**
     * @return $this
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    protected function _prepareColumns()
    {
        $this->addColumn(
            'api_entity_id',
            [
                'header' => __('ID'),
                'type' => 'number',
                'index' => 'api_entity_id',
                'header_css_class' => 'col-id',
                'column_css_class' => 'col-id'
            ]
        );

        $this->addColumn(
            'type',
            [
                'header' => __('Type'),
                'index' => 'type',
                'type' => 'options',
                'options' => \Integration\Cron\Model\Status::getOptionArray()
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
                'header' => __('Converted to Dormant'),
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
                'index'=> 'created_at',
                'type' => 'datetime',
                'header_css_class' => 'col-date',
                'column_css_class' => 'col-date'
            ]
        );

        $this->addColumn(
            'updated_at',
            [
                'header' => __('Updated At'),
                'index'=> 'updated_at',
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
        return $this->getUrl('cron/*/grid', ['_current' => true]);
    }

    /**
     * @param \Sap\ManualSync\Model\Watch|\Magento\Framework\Object $row
     * @return string
     */
    public function getRowUrl($row)
    {
        return '';
    }
}