<?php
namespace Digital\Testimonials\Block\Adminhtml\Testimonials;

class Grid extends \Magento\Backend\Block\Widget\Grid\Extended
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
     * @var \Digital\Testimonials\Model\Status
     */
    protected $_status;

    /**
     *
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
        \Digital\Testimonials\Model\Status $status,
        \Magento\Framework\Module\Manager $moduleManager,
        array $data = []
    ) {
        $this->_testimonialsFactory = $TestimonialsFactory;
        $this->_status = $status;
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
        $this->setUseAjax(true);
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

        /*$this->addColumn(
            'name',
            [
                'header' => __('Testimonial Name'),
                'index' => 'name',
            ]
        );*/
        
        $this->addColumn(
            'submitter_name',
            [
                'header' => __('Submitter Name'),
                'index' => 'submitter_name',
            ]
        );
        
        /*$this->addColumn(
            'submitter_role',
            [
                'header' => __('Testimonial Place'),
                'index' => 'submitter_role',
            ]
        );*/
        $this->addColumn(
            'rating',
            [
                'header' => __('Rating'),
                'index' => 'rating',
            ]
        );
        
        $this->addColumn(
            'sortorder',
            [
                'header' => __('Sortorder'),
                'index' => 'sortorder',
                'type' => 'number',
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

    /**
     *
     * @return $this
     */
    protected function _prepareMassaction()
    {
        $this->setMassactionIdField('testimonial_id');
        $this->getMassactionBlock()->setFormFieldName('testimonials');

        $this->getMassactionBlock()->addItem(
            'delete',
            [
                'label' => __('Delete'),
                'url' => $this->getUrl('testimonials/*/massDelete'),
                'confirm' => __('Are you sure?')
            ]
        );

        $statuses = $this->_status->getOptionArray();

        $this->getMassactionBlock()->addItem(
            'status',
            [
                'label' => __('Change status'),
                'url' => $this->getUrl('testimonials/*/massStatus', ['_current' => true]),
                'additional' => [
                    'visibility' => [
                        'name' => 'status',
                        'type' => 'select',
                        'class' => 'required-entry',
                        'label' => __('Status'),
                        'values' => $statuses
                    ]
                ]
            ]
        );

        return $this;
    }

    /**
     *
     * @return string
     */
    public function getGridUrl()
    {
        return $this->getUrl('testimonials/*/grid', ['_current' => true]);
    }

    /**
     *
     * @param \Digital\Testimonials\Model\testimonials|\Magento\Framework\Object $row
     * @return string
     */
    public function getRowUrl($row)
    {
        
        return $this->getUrl(
            'testimonials/*/edit',
            ['testimonial_id' => $row->getId()]
        );
    }
}
