<?php
namespace Digital\Testimonials\Block\Adminhtml\Testimonials;

class ExportCsv extends \Magento\Backend\Block\Widget\Grid\Extended
{
    /**
     * @var \Magento\Framework\Module\Manager
     */
    protected $moduleManager;

    /**
     * @var \Digital\Testimonials\Model\testimonialsFactory
     */
    protected $_testimonialsFactory;
    
    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Backend\Helper\Data $backendHelper
     * @param \Digital\Testimonials\Model\testimonialsFactory $testimonialsFactory
     * @param \Digital\Testimonials\Model\Status $status
     * @param \Magento\Framework\Module\Manager $moduleManager
     * @param array $data
     *
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Backend\Helper\Data $backendHelper,
        \Digital\Testimonials\Model\TestimonialsFactory $TestimonialsFactory,
        \Magento\Framework\Module\Manager $moduleManager,
        array $data = []
    ) {
        $this->_testimonialsFactory = $TestimonialsFactory;
        $this->moduleManager = $moduleManager;
        parent::__construct($context, $backendHelper, $data);
    }

    /**
     *
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setId('postGrid');
        $this->setDefaultSort('testimonial_id');
        $this->setDefaultDir('DESC');
        $this->setSaveParametersInSession(true);
        $this->setUseAjax(false);
        $this->setVarNameFilter('post_filter');
    }

    /**
     *
     * @return $this
     */
    protected function _prepareCollection()
    {
        $collection = $this->_testimonialsFactory->create()->getCollection();
        $this->setCollection($collection);

        parent::_prepareCollection();

        return $this;
    }

    /**
     *
     * @return $this
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    protected function _prepareColumns()
    {
        $this->addColumn(
            'testimonial_id',
            [
                'header' => __('ID'),
                'type' => 'number',
                'index' => 'testimonial_id',
                'header_css_class' => 'col-id',
                'column_css_class' => 'col-id'
            ]
        );

        $this->addColumn(
            'name',
            [
                'header' => __('Testimonial Name'),
                'index' => 'name',
            ]
        );

        $this->addColumn(
            'content',
            [
                'header' => __('Description'),
                'index' => 'content',
            ]
        );
        
        $this->addColumn(
            'submitter_name',
            [
                'header' => __('Submitter Name'),
                'index' => 'submitter_name',
            ]
        );
        
        $this->addColumn(
            'submitter_role',
            [
                'header' => __('Testimonial Place'),
                'index' => 'submitter_role',
            ]
        );
        
        $this->addColumn(
            'sortorder',
            [
                'header' => __('Sortorder'),
                'index' => 'sortorder',
            ]
        );
                              
        $this->addColumn(
            'status',
            [
                'header' => __('Status'),
                'index' => 'status',
                'type' => 'options',
                'options' => \Digital\Testimonials\Model\Status::getOptionArray()
            ]
        );
        
        $this->addExportType($this->getUrl('testimonials/*/exportCsv', ['_current' => true]), __('CSV'));
        $this->addExportType($this->getUrl('testimonials/*/exportExcel', ['_current' => true]), __('Excel XML'));

        $block = $this->getLayout()->getBlock('grid.bottom.links');
        if ($block) {
            $this->setChild('grid.bottom.links', $block);
        }

        return parent::_prepareColumns();
    }
}
