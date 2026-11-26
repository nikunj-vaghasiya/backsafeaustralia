<?php

namespace Digital\Productenquiry\Block\Adminhtml\Productenquiry\Edit\Tab;

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
        $model = $this->_coreRegistry->registry('productenquiry');

        /*
         * Checking if user have permissions to save information
         */
        if ($this->_isAllowedAction('Digital_Productenquiry::save')) {
            $isElementDisabled = false;
        } else {
            $isElementDisabled = true;
        }

        /** @var \Magento\Framework\Data\Form $form */
        $form = $this->_formFactory->create();

        $form->setHtmlIdPrefix('productenquiry_main_');

        $fieldset = $form->addFieldset('base_fieldset', ['legend' => __('Request Quote')]);

        if ($model->getId()) {
            $fieldset->addField('enquiry_id', 'hidden', ['name' => 'enquiry_id']);
        }

        if ($model->getEmail()) {
            $fieldset->addField(
                'email',
                'label',
                [
                    'name' => 'email',
                    'label' => __('Email Address:'),
                    'disabled' => $isElementDisabled
                ]
            );
        }

        if ($model->getFirstName()) {
            $fieldset->addField(
                'first_name',
                'label',
                [
                    'name' => 'first_name',
                    'label' => __('First Name :'),
                    'disabled' => $isElementDisabled
                ]
            );
        }
        if ($model->getLastName()) {
            $fieldset->addField(
                'last_name',
                'label',
                [
                    'name' => 'last_name',
                    'label' => __('Last Name :'),
                    'disabled' => $isElementDisabled
                ]
            );
        }
        if ($model->getCompany()) {
            $fieldset->addField(
                'company',
                'label',
                [
                    'name' => 'company',
                    'label' => __('Company :'),
                    'disabled' => $isElementDisabled
                ]
            );
        }

        if ($model->getPhoneNumber()) {
            $fieldset->addField(
                'phone_number',
                'label',
                [
                    'name' => 'phone',
                    'label' => __('Phone :'),
                    'disabled' => $isElementDisabled
                ]
            );
        }

        if ($model->getSiteName()) {
            $fieldset->addField(
                'site_name',
                'label',
                [
                    'name' => 'site_name',
                    'label' => __('Site Name :'),
                    'disabled' => $isElementDisabled
                ]
            );
        }

        if ($model->getState()) {
            $fieldset->addField(
                'state',
                'label',
                [
                    'name' => 'state',
                    'label' => __('Delivery State :'),
                    'disabled' => $isElementDisabled
                ]
            );
        }

        if ($model->getPostcode()) {
            $fieldset->addField(
                'postcode',
                'label',
                [
                    'name' => 'postcode',
                    'label' => __('Delivery Postcode :'),
                    'disabled' => $isElementDisabled
                ]
            );
        }

        /* if ($model->getShipOutsideAustralia()) {
            $getShipOutsideAustralia = $model->getShipOutsideAustralia();
            if ($getShipOutsideAustralia == 1) {
                $getShipOutsideAustralia_Data = 'Yes';
            } else {
                $getShipOutsideAustralia_Data = 'No';
            }
            $fieldset->addField(
                'ship_outside_australia',
                'note',
                [
                    'name' => 'ship_outside_australia',
                    'label' => __('Ship Outside Australia :'),
                    'text' => $getShipOutsideAustralia_Data,
                    'disabled' => $isElementDisabled
                ]
            );
        } */

        if ($model->getProducts()) {
            $fieldset->addType(
                'htmldata',
                \Digital\Productenquiry\Block\Adminhtml\Productenquiry\Renderer\Productsedit::class
            );
            $products = unserialize($model->getProducts());
            $productData = '';
            foreach ($products as $product) {
                $productData .= $product['name'] . '<br>';
            }

            $fieldset->addField(
                'products',
                'note',
                [
                    'name' => 'products',
                    'label' => __('Products :'),
                    'text' => $productData,
                    'disabled' => $isElementDisabled
                ]
            );
        }

        if ($model->getMessage()) {
            $fieldset->addField(
                'message',
                'label',
                [
                    'name' => 'message',
                    'label' => __('Message :'),
                    'disabled' => $isElementDisabled
                ]
            );
        }
        
        if ($model->getCreatedAt()) {
            $fieldset->addField(
                'created_at',
                'label',
                [
                    'name' => 'created_at',
                    'label' => __('Date :'),
                    'disabled' => $isElementDisabled
                ]
            );
        }
        $this->_eventManager->dispatch('adminhtml_productenquiry_edit_tab_main_prepare_form', ['form' => $form]);
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
        return __('Request Quote Details');
    }

    /**
     * Prepare title for tab
     *
     * @return string
     */
    public function getTabTitle()
    {
        return __('Request Quote Details');
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
}
