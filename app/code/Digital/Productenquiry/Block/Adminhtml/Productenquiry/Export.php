<?php
namespace Digital\Productenquiry\Block\Adminhtml\Productenquiry;

class Export extends \Magento\Backend\Block\Widget\Grid\Extended
{
    protected $_collectionFactory;

    protected $_productenquiry;

    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Backend\Helper\Data $backendHelper,
        \Digital\Productenquiry\Model\Productenquiry $productenquiry,
        \Digital\Productenquiry\Model\ResourceModel\Productenquiry\CollectionFactory $collectionFactory,
        array $data = []
    ) {
        $this->_collectionFactory = $collectionFactory;
        $this->_productenquiry = $productenquiry;
        parent::__construct($context, $backendHelper, $data);
    }

    protected function _construct()
    {
        parent::_construct();
        $this->setId('productenquiryGrid');
        $this->setDefaultSort('enquiry_id');
        $this->setDefaultDir('DESC');
        $this->setUseAjax(true);
        $this->setSaveParametersInSession(true);
    }

    protected function _prepareCollection()
    {
        $collection = $this->_collectionFactory->create();
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }
   
    protected function _prepareColumns()
    {
        $this->addColumn('enquiry_id', [
            'header'    => __('ID'),
            'index'     => 'enquiry_id',
        ]);
        
        $this->addColumn('first_name', ['header' => __('First Name'), 'index' => 'first_name']);
        $this->addColumn('last_name', ['header' => __('Last Name'), 'index' => 'last_name']);
        $this->addColumn('email', ['header' => __('Email Address'), 'index' => 'email']);
        $this->addColumn('company', ['header' => __('Company'), 'index' => 'company']);
        $this->addColumn('phone', ['header' => __('Phone'), 'index' => 'phone']);
        
        $this->addColumn('street', ['header' => __('Delivery Street Address'), 'index' => 'street']);
        $this->addColumn('city', ['header' => __('Delivery Suburb'), 'index' => 'city']);
        $this->addColumn('state', ['header' => __('Delivery State'), 'index' => 'state']);
        $this->addColumn('postcode', ['header' => __('Delivery Postcode'), 'index' => 'postcode']);
        
        $this->addColumn(
            'products',
            ['header' => __('Products'),
                          'index' => 'products',
            'renderer' =>  \Digital\Productenquiry\Block\Adminhtml\Productenquiry\Renderer\Productsexport::class
                         ]
        );
        
        $this->addColumn('comment', ['header' => __('Comment'), 'index' => 'comment']);
        
        $this->addColumn('created_at', ['header' => __('Date'), 'index' => 'created_at']);
        
        return parent::_prepareColumns();
    }
}
