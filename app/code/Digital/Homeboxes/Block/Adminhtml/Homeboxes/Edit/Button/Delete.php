<?php

namespace Digital\Homeboxes\Block\Adminhtml\Homeboxes\Edit\Button;

use Magento\Backend\Block\Widget\Context;
use Magento\Framework\View\Element\UiComponent\Control\ButtonProviderInterface;

class Delete extends Generic implements ButtonProviderInterface
{
    /**
     * Get context
     *
     * @var Context
     */
    protected $context;

    /**
     * Delete constructor.
     *
     * @param Context $context
     */
    public function __construct(
        Context $context
    ) {
        $this->context = $context;
    }

    /**
     * Delete button
     *
     * @return array
     */
    public function getButtonData()
    {
        if ($id = $this->context->getRequest()->getParam('id', false)) {
            return [
                'id' => 'delete',
                'label' => __('Delete'),
                'on_click' => "deleteConfirm('" . __('Are you sure you want to delete this?') . "', '"
                . $this->getDeleteUrl(['id' => $id]) . "', {data: {}})",
                'class' => 'delete',
                'sort_order' => 30,
            ];
        }

        return [];
    }

    /**
     * Get delete
     *
     * @param array $args
     * @return string
     */
    public function getDeleteUrl(array $args = [])
    {
        $params = array_merge($this->getDefaultUrlParams(), $args);
        return $this->getUrl('*/*/delete', $params);
    }

    /**
     * Get defaulturl
     *
     * @return array
     */
    protected function getDefaultUrlParams()
    {
        return ['_current' => true, '_query' => ['isAjax' => null]];
    }
}
