<?php
namespace Digital\Productguide\Block\Adminhtml\Productguide\Edit\Tab;

/**
 * Contact page edit form main tab
 */

class Main extends \Magento\Backend\Block\Widget\Form\Generic implements \Magento\Backend\Block\Widget\Tab\TabInterface
{
    /**
     * @var \Magento\Store\Model\System\Store
     */

    protected $_systemStore;

    /**
     * @param \Magento\Backend\Block\Template\Context   $context
     * @param \Magento\Framework\Registry               $registry
     * @param \Magento\Framework\Data\FormFactory       $formFactory
     * @param \Magento\Store\Model\System\Store         $systemStore
     * @param array                                     $data
     */

    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Data\FormFactory $formFactory,
        \Magento\Store\Model\System\Store $systemStore,
        array $data = []
    ) {
        $this->_systemStore = $systemStore;
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
        $model = $this->_coreRegistry->registry('productguide');
        /*
         * Checking if user have permissions to save information
         */
        if ($this->_isAllowedAction('Digital_Productguide::save')) {
            $isElementDisabled = false;
            $isDisabled = false;
        } else {
            $isElementDisabled = true;
            $isDisabled = false;
        }

        /** @var \Magento\Framework\Data\Form $form */

        $form = $this->_formFactory->create();
        $form->setHtmlIdPrefix('productguide_main_');
        $fieldset = $form->addFieldset('base_fieldset', ['legend' => __('Product Guide Enquiry Information')]);
        if ($model->getId()) {
            $fieldset->addField('productguide_id', 'hidden', ['name' => 'productguide_id']);
        }

        if ($model->getFirstname()) {
            $fieldset->addField(
                'firstname',
                'label',
                [
                    'name' => 'firstname',
                    'label' => __('First Name:'),
                    'title' => __('First Name'),
                    'disabled' => $isElementDisabled,
                ]
            );
        }

        if ($model->getLastname()) {
            $fieldset->addField(
                'lastname',
                'label',
                [
                    'name' => 'lastname',
                    'label' => __('Last Name:'),
                    'title' => __('Last Name'),
                    'disabled' => $isElementDisabled,
                ]
            );
        }

        if ($model->getCompany()) {
            $fieldset->addField(
                'company',
                'label',
                [
                    'name' => 'company',
                    'label' => __('Company:'),
                    'title' => __('Company'),
                    'disabled' => $isElementDisabled,
                ]
            );
        }

        if ($model->getEmail()) {
            $fieldset->addField(
                'email',
                'label',
                [
                    'name' => 'email',
                    'label' => __('Email:'),
                    'title' => __('Email'),
                    'disabled' => $isElementDisabled,
                ]
            );
        }

        if ($model->getStreet()) {
            $fieldset->addField(
                'street',
                'label',
                [
                    'name' => 'street',
                    'label' => __('Street Address:'),
                    'title' => __('Street Address'),
                    'disabled' => $isElementDisabled,
                ]
            );
        }

        if ($model->getCity()) {
            $fieldset->addField(
                'city',
                'label',
                [
                    'name' => 'city',
                    'label' => __('City:'),
                    'title' => __('City'),
                    'disabled' => $isElementDisabled,
                ]
            );
        }

        if ($model->getState()) {
            $fieldset->addField(
                'state',
                'label',
                [
                    'name' => 'state',
                    'label' => __('State:'),
                    'title' => __('State'),
                    'disabled' => $isElementDisabled,
                ]
            );
        }

        if ($model->getPostcode()) {
            $fieldset->addField(
                'postcode',
                'label',
                [
                    'name' => 'postcode',
                    'label' => __('Postcode:'),
                    'title' => __('Postcode'),
                    'disabled' => $isElementDisabled,
                ]
            );
        }

        if ($model->getPhone()) {
            $fieldset->addField(
                'phone',
                'label',
                [
                    'name' => 'phone',
                    'label' => __('Phone:'),
                    'title' => __('Phone'),
                    'disabled' => $isElementDisabled,
                ]
            );
        }

        $this->_eventManager->dispatch('adminhtml_productguide_edit_tab_main_prepare_form', ['form' => $form]);
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
        return __('Product Guide Enquiry Information');
    }

    /**
     * Prepare title for tab
     *
     * @return string
     */

    public function getTabTitle()
    {
        return __('Product Guide Enquiry Information');
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
}
