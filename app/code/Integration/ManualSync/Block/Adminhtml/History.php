<?php
namespace Integration\ManualSync\Block\Adminhtml;

class History extends \Magento\Backend\Block\Widget\Container
{
    /**
     * @var string
     */
    protected $_template = 'history/grid.phtml';

    /**
     * @param \Magento\Backend\Block\Widget\Context $context
     * @param array $data
     */
    public function __construct(\Magento\Backend\Block\Widget\Context $context,array $data = [])
    {
        parent::__construct($context, $data);
    }

    /**
     * Prepare button and grid
     *
     * @return \Integration\ManualSync\Block\Adminhtml\History
     */
    protected function _prepareLayout()
    {
        $addButtonProps = [
            'id' => 'add',
            'label' => __('Add History'),
            'class' => 'add primary',
            'class_name' => 'Magento\Backend\Block\Widget\Button',
            'onclick' => "setLocation('" . $this->_getCreateUrl() . "')"
        ];
        $this->buttonList->add('add', $addButtonProps);
		$this->buttonList->remove('add');

        $this->setChild('grid', $this->getLayout()->createBlock('Integration\ManualSync\Block\Adminhtml\History\Grid', 'manualsync.history.grid'));
        return parent::_prepareLayout();
    }

    /**
     *
     *
     * @return array
     */
    protected function _getAddButtonOptions()
    {
        $splitButtonOptions[] = [
            'label' => __('Add History'),
            'onclick' => "setLocation('" . $this->_getCreateUrl() . "')"
        ];

        return $splitButtonOptions;
    }

    /**
     *
     *
     * @param string $type
     * @return string
     */
    protected function _getCreateUrl()
    {
        return $this->getUrl(
            'manualsync/*/new'
        );
    }

    /**
     * Render grid
     *
     * @return string
     */
    public function getGridHtml()
    {
        return $this->getChildHtml('grid');
    }
}
