<?php
namespace Dcs\Downloadcatalogue\Block\Adminhtml\Downloadcatalogue\Edit\Tab;

/**
 * Cms page edit form main tab
 */
class Main extends \Magento\Backend\Block\Widget\Form\Generic implements \Magento\Backend\Block\Widget\Tab\TabInterface
{
    /**
     * @var \Magento\Store\Model\System\Store
     */
    protected $_systemStore;

    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Framework\Data\FormFactory $formFactory
     * @param \Magento\Store\Model\System\Store $systemStore
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        /*\Dcs\Downloadcatalogue\Helper\Data $downloadcatalogueHelper,*/
        \Magento\Framework\Data\FormFactory $formFactory,
        \Magento\Store\Model\System\Store $systemStore,
        array $data = []
    ) {
        $this->_systemStore = $systemStore;
        //$this->downloadcatalogueHelper = $downloadcatalogueHelper;
        parent::__construct($context, $registry, $formFactory, $data);
    }

    /**
     * Prepare form
     *
     * @return $this
     */
    protected function _prepareForm()
    {
        
        /* @var $model \Magento\Cms\Model\Page */
        $model = $this->_coreRegistry->registry('downloadcatalogue');

        /*
         * Checking if user have permissions to save information
         */
        if ($this->_isAllowedAction('Dcs_Downloadcatalogue::save')) {
            $isElementDisabled = false;
        } else {
            $isElementDisabled = true;
        }

        /** @var \Magento\Framework\Data\Form $form */
        $form = $this->_formFactory->create();

        $form->setHtmlIdPrefix('downloadcatalogue_main_');

        $fieldset = $form->addFieldset('base_fieldset', ['legend' => __('Catalogue Information')]);

        if ($model->getId()) {
            $fieldset->addField('downloadcatalogue_id', 'hidden', ['name' => 'downloadcatalogue_id']);
        }

        $fieldset->addField(
            'name',
            'text',
            [
                'name' => 'name',
                'label' => __('Name'),
                'title' => __('Name'),
                'required' => true,
                'disabled' => $isElementDisabled
            ]
        );

        /* $fieldset->addField(
            'short_description',
            'textarea',
            [
                'name' => 'short_description',
                'label' => __('Short Description'),
                'title' => __('Short Description'),
                'required' => false,
                'disabled' => $isElementDisabled
            ]
        ); */

        $fieldset->addField(
            'image',
            'image',
            [
                'name' => 'image',
                'label' => __('Image'),
                'title' => __('Image'),
                'required'  => false,
                'note' => '(Dimensions: 119px * 168px)',
                'disabled' => $isElementDisabled
            ]
        );
        $fieldset->addField(
            'catalogue_file',
            'image',
            [
                'name' => 'catalogue_file',
                'label' => __('Catalogue File'),
                'title' => __('Catalogue File'),
                'required'  => false,
                'note' => 'Allow PDF file. <br> Maximum upload file size: 20MB.',
                'disabled' => $isElementDisabled
            ]
        );

        $fieldset->addField(
            'button_url',
            'text',
            [
                'name' => 'button_url',
                'label' => __('Button URL'),
                'title' => __('Button URL'),
                'required' => false,
                'disabled' => $isElementDisabled
            ]
        );
        
        $fieldset->addField(
            'rank',
            'text',
            [
                'name' => 'rank',
                'label' => __('Rank'),
                'title' => __('Rank'),
                'required' => false,
                'class'=>'validate-not-negative-number',
                'disabled' => $isElementDisabled
            ]
        );
        $fieldset->addField(
            'status',
            'select',
            [
                'label' => __('status'),
                'title' => __('status'),
                'name' => 'status',
                'required' => false,
               'values' => [
                            ['value'=>'1','label'=>'Enable'],
                            ['value'=>'2','label'=>'Disable'],
                            
                       ],
                //'disabled' => $isDisabled
            ]
        );

        $this->_eventManager->dispatch('adminhtml_downloadcatalogue_edit_tab_main_prepare_form', ['form' => $form]);

        $form->setValues($model->getData());
        $this->setForm($form);

        return parent::_prepareForm();
    }

    /**
     * Prepare label for tab
     *
     * @return string
     */
    public function getTabLabel()
    {
        return __('Catalogue Information');
    }

    /**
     * Prepare title for tab
     *
     * @return string
     */
    public function getTabTitle()
    {
        return __('Catalogue Information');
    }

    /**
     * {@inheritdoc}
     */
    public function canShowTab()
    {
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function isHidden()
    {
        return false;
    }

    /**
     * Check permission for passed action
     *
     * @param string $resourceId
     * @return bool
     */
    protected function _isAllowedAction($resourceId)
    {
        return $this->_authorization->isAllowed($resourceId);
    }
    protected function _getAdditionalElementTypes()
    {
        return ['image' => \Dcs\Downloadcatalogue\Block\Adminhtml\Form\Element\Image::class];
    }
}
