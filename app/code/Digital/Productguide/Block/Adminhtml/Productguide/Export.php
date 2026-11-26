<?php
namespace Digital\Productguide\Block\Adminhtml\Productguide;

/**
 * Adminhtml Productguide grid
 */
class Export extends \Magento\Backend\Block\Widget\Grid\Extended
{
    /**
     * @var \Digital\Productguide\Model\ResourceModel\Productguide\CollectionFactory
     */
    protected $_collectionFactory;

    /**
     * @var \Digital\Productguide\Model\Productguide
     */
    protected $_productguide;

    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Backend\Helper\Data $backendHelper
     * @param \Magento\Config\Model\Config\Source\Yesno $booleanOptions
     * @param \Digital\Productguide\Model\Productguide $productguide
     * @param \Digital\Productguide\Model\ResourceModel\Productguide\CollectionFactory $collectionFactory
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Backend\Helper\Data $backendHelper,
        \Magento\Config\Model\Config\Source\Yesno $booleanOptions,
        \Digital\Productguide\Model\Productguide $productguide,
        \Digital\Productguide\Model\ResourceModel\Productguide\CollectionFactory $collectionFactory,
        array $data = []
    ) {
        $this->_collectionFactory = $collectionFactory;
        $this->booleanOptions = $booleanOptions;
        $this->_productguide = $productguide;
        parent::__construct($context, $backendHelper, $data);
    }

    /**
     * Contact Grid
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
        $this->addColumn('street', ['header' => __('Street Address'), 'index' => 'street']);
        $this->addColumn('city', ['header' => __('City'), 'index' => 'city']);
        $this->addColumn('state', ['header' => __('State'), 'index' => 'state']);
        $this->addColumn('postcode', ['header' => __('Postal Code'), 'index' => 'postcode']);
        $this->addColumn('phone', ['header' => __('Phone Number'), 'index' => 'phone']);

        /*$this->addColumn(
            'stores',
            [
                'header' => __('Store Views'),
                'index' => 'stores',
                'type' => 'store',
                'store_all' => true,
                'store_view' => true,
                'renderer' => \Digital\Productguide\Block\Adminhtml\Productguide\Grid\Column\Renderer\Store::class,
            ]
        );*/
        return parent::_prepareColumns();
    }

    /**
     * Prepare columns
     *
     * @return \Magento\Backend\Block\Widget\Grid\Extended
     */
    protected function _prepareMassaction()
    {
        $this->setMassactionIdField('entity_id');
        $this->getMassactionBlock()->setFormFieldName('productguide_id');
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
