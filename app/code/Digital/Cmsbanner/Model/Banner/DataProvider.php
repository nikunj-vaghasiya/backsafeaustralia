<?php

declare(strict_types=1);

namespace Digital\Cmsbanner\Model\Banner;

use Digital\Cmsbanner\Model\ResourceModel\Banner\Collection;
use Digital\Cmsbanner\Model\ResourceModel\Banner\CollectionFactory;
use Magento\Catalog\Model\ImageUploader;
use Magento\Framework\App\Request\DataPersistorInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\UrlInterface;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Ui\DataProvider\AbstractDataProvider;

class DataProvider extends AbstractDataProvider
{
    private array $loadedData = [];

    public function __construct(
        string $name,
        string $primaryFieldName,
        string $requestFieldName,
        CollectionFactory $collectionFactory,
        private readonly DataPersistorInterface $dataPersistor,
        private readonly StoreManagerInterface $storeManager,
        private readonly ImageUploader $imageUploader,
        array $meta = [],
        array $data = []
    ) {
        $this->collection = $collectionFactory->create();
        parent::__construct($name, $primaryFieldName, $requestFieldName, $meta, $data);
    }

    public function getData(): array
    {
        if (!empty($this->loadedData)) {
            return $this->loadedData;
        }

        /** @var Collection $items */
        $items = $this->collection->getItems();

        foreach ($items as $banner) {
            $data = $banner->getData();

            // Prepare image for UI component
            if (!empty($data['image'])) {
                $imageName = $data['image'];
                $data['image'] = [
                    [
                        'name' => $imageName,
                        'url'  => $this->getImageUrl($imageName),
                        'size' => 0,
                        'type' => 'image',
                    ]
                ];
            }

            if (!empty($data['mobile_image'])) {
                $mobileImageName = $data['mobile_image'];
                $data['mobile_image'] = [[
                    'name' => $mobileImageName,
                    'url'  => $this->getImageUrl($mobileImageName),
                    'size' => 0,
                    'type' => 'image',
                ]];
            }

            // Prepare cms_pages as array for multi-select
            if (!empty($data['cms_pages'])) {
                $data['cms_pages'] = explode(',', $data['cms_pages']);
            } else {
                $data['cms_pages'] = [];
            }

            $this->loadedData[$banner->getId()] = $data;
        }

        $persistedData = $this->dataPersistor->get('digital_cmsbanner');
        if (!empty($persistedData)) {
            $banner = $this->collection->getNewEmptyItem();
            $banner->setData($persistedData);
            $this->loadedData[$banner->getId()] = $banner->getData();
            $this->dataPersistor->clear('digital_cmsbanner');
        }

        return $this->loadedData;
    }

    private function getImageUrl(string $imageName): string
    {
        try {
            $baseUrl = $this->storeManager->getStore()->getBaseUrl(UrlInterface::URL_TYPE_MEDIA);
            return $baseUrl . 'digital/cmsbanner/' . $imageName;
        } catch (LocalizedException $e) {
            return '';
        }
    }
}
