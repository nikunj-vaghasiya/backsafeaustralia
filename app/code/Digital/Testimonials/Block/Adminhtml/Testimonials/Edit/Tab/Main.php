<?php

namespace Digital\Testimonials\Block\Adminhtml\Testimonials\Edit\Tab;

use Magento\Cms\Model\Wysiwyg\Config;

/**
 * Testimonials edit form main tab
 */

class Main extends \Magento\Backend\Block\Widget\Form\Generic implements \Magento\Backend\Block\Widget\Tab\TabInterface
{
    /**
     * @var \Magento\Store\Model\System\Store
     */
    protected $_systemStore;

    /**
     * @var \Digital\Testimonials\Model\Status
     */
    protected $_status;

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
        \Magento\Framework\Data\FormFactory $formFactory,
        \Magento\Store\Model\System\Store $systemStore,
        Config $wysiwygConfig,
        \Digital\Testimonials\Model\Status $status,
        array $data = []
    ) {
        $this->_systemStore = $systemStore;
        $this->_wysiwygConfig = $wysiwygConfig;
        $this->_status = $status;
        parent::__construct($context, $registry, $formFactory, $data);
    }

    /**
     * Prepare form
     *
     * @return $this
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */

    protected function _prepareForm()
    {
        /* @var $model \Digital\Testimonials\Model\BlogPosts */
        $model = $this->_coreRegistry->registry('testimonials');
        $isElementDisabled = false;

        /** @var \Magento\Framework\Data\Form $form */
        $form = $this->_formFactory->create();

        $form->setHtmlIdPrefix('page_');
        $fieldset = $form->addFieldset('base_fieldset', ['legend' => __('Testimonial Information')]);

        if ($model->getId()) {
            $fieldset->addField('testimonial_id', 'hidden', ['name' => 'testimonial_id']);
        }

        $fieldset->addField(
            'submitter_name',
            'text',
            [
                'name' => 'submitter_name',
                'label' => __('Submitter Name'),
                'title' => __('Submitter Name'),
                'required' => true,
                'disabled' => $isElementDisabled
            ]
        );
        
        $fieldset->addField(
            'rating',
            'text',
            [
                'name' => 'rating',
                'label' => __('Rating'),
                'title' => __('Rating'),
                'required' => true,
                'note' => 'Please rate between 1 and 5.',
                'disabled' => $isElementDisabled
            ]
        );

        $fieldset->addField(
            'content',
            'editor',
            [
                'name' => 'content',
                'label' => __('Description'),
                'title' => __('Description'),
                'wysiwyg' => true,
                'required' => true,
                'config'    => $this->_wysiwygConfig->getConfig()
            ]
        );

        /*$fieldset->addField(
            'submitter_role',
            'text',
            [
                'name' => 'submitter_role',
                'label' => __('Testimonial Place'),
                'title' => __('Testimonial Place'),
                'disabled' => $isElementDisabled
            ]
        );*/

        $fieldset->addField(
            'sortorder',
            'text',
            [
                'name' => 'sortorder',
                'label' => __('Sortorder'),
                'title' => __('sortorder'),
                'disabled' => $isElementDisabled
            ]
        );

        $fieldset->addField(
            'status',
            'select',
            [
                'label' => __('Status'),
                'title' => __('Status'),
                'name' => 'status',
                'options' => \Digital\Testimonials\Model\Status::getOptionArray(),
                'disabled' => $isElementDisabled
            ]
        );

        if (!$model->getId()) {
            $model->setData('is_active', $isElementDisabled ? '0' : '1');
        }

        $form->setValues($model->getData());
        $this->setForm($form);
        return parent::_prepareForm();
    }

    /**
     * Prepare label for tab
     *
     * @return \Magento\Framework\Phrase
     */

    public function getTabLabel()
    {
        return __('Testimonial Information');
    }

    /**
     * Prepare title for tab
     *
     * @return \Magento\Framework\Phrase
     */

    public function getTabTitle()
    {
        return __('Testimonial Information');
    }

    /**
     * @inheritdoc
     */

    public function canShowTab()
    {
        return true;
    }

    /**
     * @inheritdoc
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

    public function getTargetOptionArray()
    {
        return [
                    '_self' => "Self",

                    '_blank' => "New Page",

                    ];
    }
}
