<?php

namespace Digital\Projects\Block\Adminhtml\Projects;

class AssignProducts extends \Magento\Backend\Block\Template
{
    /**
     * Block template
     *
     * @var string
     */
    protected $_template = 'catalog/product/edit/assign_products.phtml';
    protected $blockGrid;
    protected $registry;
    protected $jsonEncoder;
    protected $mappingCollectionFactory;

    /**
     * AssignProducts constructor.
     *
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Framework\Json\EncoderInterface $jsonEncoder
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Json\EncoderInterface $jsonEncoder,
        \Digital\Projects\Model\ResourceModel\Products\CollectionFactory $mappingCollectionFactory,
        array $data = []
    ) {
        $this->registry = $registry;
        $this->jsonEncoder = $jsonEncoder;
        $this->mappingCollectionFactory = $mappingCollectionFactory;
        parent::__construct($context, $data);
    }

    /**
     * Retrieve instance of grid block
     *
     * @return \Magento\Framework\View\Element\BlockInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getBlockGrid()
    {
        if (null === $this->blockGrid) {
            $this->blockGrid = $this->getLayout()->createBlock(
                \Digital\Projects\Block\Adminhtml\Projects\Tab\Product::class,
                'projects.product.grid'
            );
        }
        return $this->blockGrid;
    }
    /**
     * Return HTML of grid block
     *
     * @return string
     */
    public function getGridHtml()
    {
        return $this->getBlockGrid()->toHtml();
    }
    /**
     * @return string
     */
    public function getProductsJson()
    {
        $finderId = $this->getRequest()->getParam('projects_id');
        $vProducts = $this->mappingCollectionFactory->create()
            ->addFieldToFilter('projects_id', $finderId)
            ->addFieldToSelect('product_id');
        $products = [];
        foreach ($vProducts as $pdct) {
            $products[$pdct->getProductId()] = $pdct->getProductId();
        }
        if (!empty($products)) {
            return $this->jsonEncoder->encode($products);
        }
        return '{}';
    }
    public function getProduct()
    {
        return $this->registry->registry('projects');
    }
}
