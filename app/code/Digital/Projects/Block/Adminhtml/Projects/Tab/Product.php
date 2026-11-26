<?php

namespace Digital\Projects\Block\Adminhtml\Projects\Tab;

use Magento\Backend\Block\Widget\Grid;
use Magento\Backend\Block\Widget\Grid\Column;
use Magento\Backend\Block\Widget\Grid\Extended;

class Product extends \Magento\Backend\Block\Widget\Grid\Extended
{
    /**
     * @var \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory
     */
    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry = null;
    /**
     * @var \Magento\Catalog\Model\ProductFactory
     */
    protected $proCollectionFactory;
    protected $productCollectionFactory;
    /**
     *
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Backend\Helper\Data $backendHelper
     * @param \Magento\Framework\Registry $registry
     * @param ContactFactory $attachmentFactory
     * @param \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollectionFactory
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Backend\Helper\Data $backendHelper,
        \Digital\Projects\Model\ResourceModel\Products\CollectionFactory $proCollectionFactory,
        \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollectionFactory,
        \Magento\Framework\Registry $coreRegistry,
        array $data = []
    ) {
        $this->proCollectionFactory = $proCollectionFactory;
        $this->_coreRegistry = $coreRegistry;
        $this->productCollectionFactory = $productCollectionFactory;
        parent::__construct($context, $backendHelper, $data);
    }

    /**
     * _construct
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setId('productsGrid');
        $this->setDefaultSort('entity_id');
        $this->setDefaultDir('DESC');
        $this->setSaveParametersInSession(true);
        $this->setUseAjax(true);
        if ($this->getRequest()->getParam('projects_id')) {
            $this->setDefaultFilter(['in_product' => 1]);
        }
    }

    public function getProduct()
    {
        return $this->_coreRegistry->registry('projects');
    }

    /**
     * add Column Filter To Collection
     */
    protected function _addColumnFilterToCollection($column)
    {
        if ($column->getId() == 'in_product') {
            $productIds = $this->_getSelectedProducts();
            if (empty($productIds)) {
                $productIds = 0;
            }
            if ($column->getFilter()->getValue()) {
                $this->getCollection()->addFieldToFilter('entity_id', ['in' => $productIds]);
            } else {
                if ($productIds) {
                    $this->getCollection()->addFieldToFilter('entity_id', ['nin' => $productIds]);
                }
            }
        } else {
            parent::_addColumnFilterToCollection($column);
        }
        return $this;
    }
    /**
     * prepare collection
     */
    protected function _prepareCollection()
    {
         /** to get default filter in grid */
        if ($this->getRequest()->getParam('projects_id')) {
            $this->setDefaultFilter(['in_product' => 1]);
        }

        $collection = $this->productCollectionFactory->create();
        $collection->addAttributeToSelect('name');
        $collection->addAttributeToSelect('sku');
        //$collection->addAttributeToSelect('price');

        if (!$this->getRequest()->getParam('projects_id'))
        {
            $productIds = $this->_getSelectedProducts();
            $collection->addFieldToFilter('entity_id', ['in' => $productIds]);
        }    

        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    /**
     * @return $this
     */
    protected function _prepareColumns()
    {
        $this->addColumn(
            'in_product',
            [
                'header_css_class' => 'a-center',
                'type' => 'checkbox',
                'name' => 'in_product',
                'align' => 'center',
                'index' => 'entity_id',
                'values' => $this->_getSelectedProducts(),
            ]
        );
        $this->addColumn(
            'entity_id',
            [
                'header' => __('Product ID'),
                'type' => 'number',
                'index' => 'entity_id',
                'header_css_class' => 'col-id',
                'column_css_class' => 'col-id',
            ]
        );
        $this->addColumn(
            'names',
            [
                'header' => __('Name'),
                'index' => 'name',
                'class' => 'xxx',
                'width' => '50px',
            ]
        );
        $this->addColumn(
            'sku',
            [
                'header' => __('Sku'),
                'index' => 'sku',
                'class' => 'xxx',
                'width' => '50px',
            ]
        );
       /* $this->addColumn(
            'price',
            [
                'header' => __('Price'),
                'type' => 'currency',
                'index' => 'price',
                'width' => '50px',
            ]
        ); */
        return parent::_prepareColumns();
    }

    /**
     * @return string
     */
    public function getGridUrl()
    {
        //return $this->getUrl('*/products/grid', ['_current' => true]);
        return $this->getUrl('*/*/productsGrid', ['projects_id' => $this->getRequest()->getParam('id')]);
    }

    protected function _getSelectedProducts()
    {
        $products = $this->getRequest()->getPost('selected_products');

         $projects_Id = $this->getRequest()->getParam('id'); 
        if ($products === null) {
            $vProducts = $this->proCollectionFactory->create()
                ->addFieldToFilter('projects_id', $projects_Id)
                ->addFieldToSelect('product_id');
            $products = [];
            foreach ($vProducts as $pdct) {
                $products[] = $pdct->getProductId();
            }
        }
        return $products;
    }
}
