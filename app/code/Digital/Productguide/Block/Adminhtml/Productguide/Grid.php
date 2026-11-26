<?php
namespace Digital\Productguide\Block\Adminhtml\Productguide;

/**
 * Adminhtml Productguide grid
 */
class Grid extends \Magento\Backend\Block\Widget\Grid\Extended
{
    /**
     * @var \Digital\Productguide\Model\ResourceModel\Productguide\CollectionFactory
     */
    protected $_collectionFactory;

    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Backend\Helper\Data $backendHelper
     * @param \Digital\Productguide\Model\ResourceModel\Productguide\CollectionFactory $collectionFactory
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Backend\Helper\Data $backendHelper,
        \Digital\Productguide\Model\ResourceModel\Productguide\CollectionFactory $collectionFactory,
        array $data = []
    ) {
        $this->_collectionFactory = $collectionFactory;
        parent::__construct($context, $backendHelper, $data);
    }

    /**
     * Adminhtml Productguide grid
     *
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setId('productguideGrid');
        $this->setDefaultSort('productguide_id');
        $this->setDefaultDir('DESC');
        $this->setUseAjax(true);
        $this->setSaveParametersInSession(true);
    }

    /**
     * Prepare collection
     *
     * @return \Magento\Backend\Block\Widget\Grid
     */
    protected function _prepareCollection()
    {
        $collection = $this->_collectionFactory->create();
        /* @var $collection \Digital\Productguide\Model\ResourceModel\Productguide\Collection */
        $this->setCollection($collection);

        return parent::_prepareCollection();
    }
    /**
     * Prepare columns
     *
     * @return \Magento\Backend\Block\Widget\Grid\Extended
     */
    protected function _prepareColumns()
    {
        $this->addColumn('productguide_id', [
            'header' => __('ID'),
            'index' => 'productguide_id',
        ]);
        $this->addColumn('firstname', ['header' => __('First Name'), 'index' => 'firstname']);
        $this->addColumn('lastname', ['header' => __('Last Name'), 'index' => 'lastname']);
        $this->addColumn('company', ['header' => __('Company'), 'index' => 'company']);
        $this->addColumn('email', ['header' => __('Email'), 'index' => 'email']);
        $this->addColumn('created_at', ['header' => __('Created At'), 'index' => 'created_at']);

        $this->addColumn(
            'action',
            [
                'header' => __('View'),
                'type' => 'action',
                'getter' => 'getId',
                'actions' => [
                    [
                        'caption' => __('View'),
                        'url' => [
                            'base' => '*/*/edit',
                            'params' => ['store' => $this->getRequest()->getParam('store')],
                        ],
                        'field' => 'productguide_id',
                    ],
                ],
                'sortable' => false,
                'filter' => false,
                'header_css_class' => 'col-action',
                'column_css_class' => 'col-action',
            ]
        );

        $this->addExportType($this->getUrl('productguide/*/exportCsv', ['_current' => true]), __('CSV'));

        return parent::_prepareColumns();
    }

    /**
     * Prepare Massactions
     *
     * @return \Magento\Backend\Block\Widget\Grid\Extended
     */
    protected function _prepareMassaction()
    {
        $this->setMassactionIdField('productguide_id');
        $this->getMassactionBlock()->setFormFieldName('productguide');
        $this->getMassactionBlock()->addItem(
            'delete',
            [
                'label' => __('Delete'),
                'url' => $this->getUrl('productguide/*/massDelete'),
                'confirm' => __('Are you sure?'),
            ]
        );
        return $this;
    }

    /**
     * Filter Store Condition
     *
     * @param \Magento\Framework\Data\Collection $collection
     * @param \Magento\Backend\Block\Widget\Grid\Column $column
     * @return void
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    protected function _filterStoreCondition($collection, $column)
    {
        if (!$value = $column->getFilter()->getValue()) {
            return;
        }
        $this->getCollection()->addFieldToFilter('stores', ['finset' => $value]);
    }

    /**
     * Row click url
     *
     * @param \Magento\Framework\Object $row
     * @return string
     */
    public function getRowUrl($row)
    {
        return $this->getUrl('*/*/edit', ['productguide_id' => $row->getId()]);
    }

    /**
     * Get grid url
     *
     * @return string
     */
    public function getGridUrl()
    {
        return $this->getUrl('*/*/grid', ['_current' => true]);
    }
}
