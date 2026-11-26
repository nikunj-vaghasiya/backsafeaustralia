<?php
namespace Digital\Projects\Block\Adminhtml\Button;

use Magento\Backend\Block\Widget\Context;
use Magento\Framework\View\Element\UiComponent\Control\ButtonProviderInterface;
class Delete extends Generic implements ButtonProviderInterface
{
    /**
     * @var Context
     */
    protected $context;
    /**
     * @param Context $context
     */
    public function __construct(
        Context $context
    ) {
        $this->context = $context;
    }
    /**
     * Get button data
     *
     * @return array
     */
    public function getButtonData()
    {
        $data = [];
        $projectsId = $this->context->getRequest()->getParam('id');
        if ($projectsId) {
            $data = [
                'label' => __('Delete'),
                'class' => 'delete',
                'on_click' => 'deleteConfirm(\'' . __(
                    'Are you sure you want to do this?'
                ) . '\', \'' . $this->getDeleteUrl() . '\')',
                'sort_order' => 20,
            ];
        }
        return $data;
    }
    /**
     * @return string
     */
    public function getDeleteUrl()
    {
        $projectsId = $this->context->getRequest()->getParam('id');
        return $this->getUrl('projects/projects/delete', ['projects_id' => $projectsId]);
    }
}