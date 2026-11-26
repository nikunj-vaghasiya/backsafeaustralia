<?php
namespace Digital\Projects\Ui\Component\Listing\Columns;

use Digital\Projects\Helper\Data;
use Magento\Framework\UrlInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Ui\Component\Listing\Columns\Column;

class Thumbnail extends Column
{
    /**
     * @var UrlInterface
     */
    protected $urlBuilder;
    /**
     * @var Data
     */
    private $helperData;

    /**
     * Thumbnail constructor.
     *
     * @param ContextInterface   $context
     * @param UiComponentFactory $uiComponentFactory
     * @param UrlInterface       $urlBuilder
     * @param Data               $helperData
     * @param array              $components
     * @param array              $data
     */
    public function __construct(
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        UrlInterface $urlBuilder,
        Data $helperData,
        array $components = [],
        array $data = []
    ) {
        $this->urlBuilder = $urlBuilder;
        parent::__construct($context, $uiComponentFactory, $components, $data);
        $this->helperData = $helperData;
    }

    /**
     * Prepare Data Source
     *
     * @param array $dataSource
     * @return array
     * @throws \Exception
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function prepareDataSource(array $dataSource)
    {
        if (isset($dataSource['data']['items'])) {
            $fieldName = $this->getData('name');
            foreach ($dataSource['data']['items'] as &$item) {
                if (empty($item[$fieldName])) {
                    continue;
                }
                $imageName = $item[$fieldName];
                $item[$fieldName . '_src'] = $this->helperData->getResizeImage($imageName, null, 50);
                $item[$fieldName . '_link'] = $this->urlBuilder->getUrl(
                    'projects/projects/edit',
                    ['id' => $item['projects_id'], 'store' => $this->context->getRequestParam('store')]
                );
                $item[$fieldName . '_orig_src'] = $this->helperData->getImageUrl($imageName);
            }
        }
        return $dataSource;
    }
}
