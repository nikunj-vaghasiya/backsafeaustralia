<?php
namespace Digital\BannerSlider\Model\Banner;

use Digital\BannerSlider\Api\Data\BannerInterface;
use Digital\BannerSlider\Model\ImageField;
use Digital\BannerSlider\Model\ImageUploader;
use Digital\BannerSlider\Model\ResourceModel\Banner\Collection;
use Digital\BannerSlider\Model\ResourceModel\Banner\CollectionFactory;
use Magento\Framework\Registry;
use Magento\Ui\DataProvider\AbstractDataProvider;

class DataProvider extends AbstractDataProvider
{

    /**
     * @var Collection
     */
    protected $collection;

    /**
     * @var array
     */
    protected $loadedData;

    /**
     * @var \Magento\Framework\Registry
     */
    protected $coreRegistry;

    /**
     * @var ImageUploader
     */
    private $imageUploader;

    /**
     * DataProvider constructor.
     *
     * @param name              $name
     * @param primaryfiledname  $primaryFieldName
     * @param requstfiledname   $requestFieldName
     * @param CollectionFactory $collectionFactory
     * @param ImageUploader     $imageUploader
     * @param Registry          $registry
     * @param array             $meta
     * @param array             $data
     */
    public function __construct(
        string $name,
        string $primaryFieldName,
        string $requestFieldName,
        CollectionFactory $collectionFactory,
        ImageUploader $imageUploader,
        Registry $registry,
        array $meta = [],
        array $data = []
    ) {
        $this->collection = $collectionFactory->create();
        $this->coreRegistry = $registry;
        parent::__construct($name, $primaryFieldName, $requestFieldName, $meta, $data);
        $this->imageUploader = $imageUploader;
    }

    /**
     * @inheritdoc
     */
    public function getData()
    {
        if (isset($this->loadedData)) {
            return $this->loadedData;
        }
        /** @var BannerInterface | \Digital\BannerSlider\Model\Banner $banner */
        $banner = $this->coreRegistry->registry('current_banner');

        if ($banner && $banner->getId()) {
            $bannerData = $banner->getData();
            foreach (ImageField::getField() as $field) {
                unset($bannerData[$field]);
                $imageName = $banner->getData($field);
                if ($imageSrc = $banner->getImageUrl($imageName)) {
                    try {
                        $size = $this->imageUploader->getSize($imageName);
                    } catch (\Exception $e) {
                        $size = 'undefined';
                    }
                    $bannerData[$field][] = [
                        'name' => $imageName,
                        'url' => $imageSrc,
                        'size' => $size,
                    ];
                }
            }
            $this->loadedData[$banner->getId()] = $bannerData;
        } else {
            $this->loadedData = [];
        }

        return $this->loadedData;
    }
}
