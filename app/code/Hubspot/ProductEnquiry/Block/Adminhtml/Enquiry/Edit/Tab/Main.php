<?php
namespace Hubspot\ProductEnquiry\Block\Adminhtml\Enquiry\Edit\Tab;

class Main extends \Magento\Backend\Block\Widget\Form\Generic implements
    \Magento\Backend\Block\Widget\Tab\TabInterface
{
    /** @var \Magento\Framework\Stdlib\DateTime\TimezoneInterface */
    protected $timezone;

    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Data\FormFactory $formFactory,
        \Magento\Framework\Stdlib\DateTime\TimezoneInterface $timezone,
        array $data = []
    ) {
        $this->timezone = $timezone;
        parent::__construct($context, $registry, $formFactory, $data);
    }

    protected function _prepareForm()
    {
        /** @var \Hubspot\ProductEnquiry\Model\Enquiry $model */
        $model = $this->_coreRegistry->registry('current_enquiry');

        /** @var \Magento\Framework\Data\Form $form */
        $form = $this->_formFactory->create();
        $form->setHtmlIdPrefix('productenquiry_enquiry_');

        $fieldset = $form->addFieldset('base_fieldset', ['legend' => __('Product Enquiry')]);

        if ($model && $model->getId()) {
            $fieldset->addField('entity_id', 'hidden', ['name' => 'entity_id']);
        }

        if ($model && $model->getName()) {
            $fieldset->addField('name', 'label', ['name' => 'name', 'label' => __('Name :')]);
        }
        if ($model && $model->getProductName()) {
            $productName = $model->getProductName();
            $productId = (int) $model->getProductId();
            if ($productId) {
                $editUrl = $this->getUrl('catalog/product/edit', ['id' => $productId]);
                $text = '<a href="' . $editUrl . '" target="_blank">'
                    . $this->escapeHtml($productName) . '</a>';
            } else {
                $text = $this->escapeHtml($productName);
            }
            $fieldset->addField('product_name', 'note', ['label' => __('Product :'), 'text' => $text]);
        }
        if ($model && $model->getProduct()) {
            $fieldset->addField('product', 'label', ['name' => 'product', 'label' => __('SKU :')]);
        }
        if ($model && $model->getEmail()) {
            $email = $model->getEmail();
            $fieldset->addField(
                'email',
                'note',
                [
                    'label' => __('Email :'),
                    'text'  => '<a href="mailto:' . $this->escapeHtmlAttr($email) . '">'
                        . $this->escapeHtml($email) . '</a>',
                ]
            );
        }
        if ($model && $model->getPhone()) {
            $fieldset->addField('phone', 'label', ['name' => 'phone', 'label' => __('Phone :')]);
        }
        if ($model && $model->getEnquiry()) {
            $fieldset->addField('enquiry', 'label', ['name' => 'enquiry', 'label' => __('Enquiry :')]);
        }
        if ($model && $model->getComments()) {
            $fieldset->addField('comments', 'label', ['name' => 'comments', 'label' => __('Comments :')]);
        }
        if ($model && $model->getCreatedAt()) {
            $localCreated = $this->timezone->formatDateTime(
                new \DateTime($model->getCreatedAt(), new \DateTimeZone('UTC')),
                \IntlDateFormatter::MEDIUM,
                \IntlDateFormatter::MEDIUM
            );
            $fieldset->addField('created_at', 'note', [
                'label' => __('Submitted On :'),
                'text'  => $this->escapeHtml($localCreated),
            ]);
        }

        if ($model) {
            $form->setValues($model->getData());
        }
        $this->setForm($form);
        return parent::_prepareForm();
    }

    public function getTabLabel()
    {
        return __('Enquiry Details');
    }

    public function getTabTitle()
    {
        return __('Enquiry Details');
    }

    public function canShowTab()
    {
        return true;
    }

    public function isHidden()
    {
        return false;
    }
}