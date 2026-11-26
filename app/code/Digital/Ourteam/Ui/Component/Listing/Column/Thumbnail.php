<?php
declare(strict_types=1);

namespace Digital\Ourteam\Ui\Component\Listing\Column;

use Digital\Ourteam\Helper\Data;
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
     */
    public function prepareDataSource(array $dataSource)
    {
        if (!isset($dataSource['data']['items'])) {
            return $dataSource;
        }

        // Column name as declared in the listing XML ("image")
        $fieldName = (string)$this->getData('name');

        foreach ($dataSource['data']['items'] as &$item) {
            if (empty($item[$fieldName])) {
                continue;
            }

            $imageName = $item[$fieldName];

            try {
                $originalUrl = $this->helperData->getImageUrl($imageName);
                $thumbnailUrl = $this->helperData->getResizeImage($imageName, 75, 75);
            } catch (\Exception $e) {
                // Missing / unreadable file - leave the cell empty rather than break the grid
                continue;
            }

            if (!$originalUrl) {
                continue;
            }

            $item[$fieldName . '_src'] = $thumbnailUrl ?: $originalUrl;
            $item[$fieldName . '_orig_src'] = $originalUrl;
            $item[$fieldName . '_alt'] = $item['name'] ?? '';
            $item[$fieldName . '_link'] = $this->urlBuilder->getUrl(
                'ourteam/team/edit',
                ['id' => $item['team_id'], 'store' => $this->context->getRequestParam('store')]
            );
        }

        return $dataSource;
    }
}
