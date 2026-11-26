<?php
namespace Integration\Cron\Block\Adminhtml;

class Statistics extends \Magento\Backend\Block\Widget\Container
{
    /**
     * @var string
     */
    protected $_template = 'statistics/grid.phtml';

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
     * @return \Sap\ManualSync\Block\Adminhtml\History
     */
    protected function _prepareLayout()
    {
        $addButtonProps = [
            'id' => 'add',
            'label' => __('Add Statistics'),
            'class' => 'add primary',
            'class_name' => 'Magento\Backend\Block\Widget\Button',
            'onclick' => "setLocation('" . $this->_getCreateUrl() . "')"
        ];
        $this->buttonList->add('add', $addButtonProps);
		$this->buttonList->remove('add');

        $this->setChild('grid', $this->getLayout()->createBlock('Integration\Cron\Block\Adminhtml\Statistics\Grid', 'cron.statistics.grid'));
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
            'label' => __('Add Statistics'),
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
            'cron/*/new'
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
