<?php
/**
 * File: Block/Adminhtml/Config/VideoLinks.php
 * Renders the dynamic "Add Row" table in System > Configuration.
 */
declare(strict_types=1);

namespace Digital\Homeboxes\Block\Adminhtml\Config;

use Magento\Config\Block\System\Config\Form\Field\FieldArray\AbstractFieldArray;
use Magento\Framework\DataObject;

class VideoLinks extends AbstractFieldArray
{
    /**
     * Define the columns that appear in each dynamic row.
     * '_default' sets the placeholder / default value for the input.
     */
    protected function _prepareToRender(): void
    {
        $this->addColumn('video_title', [
            'label'    => __('Video Title'),
            'class'    => 'required-entry',   // triggers Magento's required validation
            'style'    => 'width:250px',
            '_default' => '',
        ]);

        $this->addColumn('video_url', [
            'label'    => __('Video URL'),
            'class'    => 'required-entry validate-url', // built-in URL format validation
            'style'    => 'width:350px',
            '_default' => '',
        ]);

        // Shows the "Add" button beneath the table
        $this->_addAfter       = false;
        $this->_addButtonLabel = __('Add Video');
    }

    protected function _prepareArrayRow(DataObject $row): void
    {
        $options = [];
        $templete = $row->getTemplete();        
        $row->setData('option_extra_attrs', $options);
    }
}
