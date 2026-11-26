<?php
namespace Digital\Homeboxes\Model\Boxes;

use Digital\Homeboxes\Api\Data\BoxesInterface;
use Digital\Homeboxes\Model\ImageField;
use Digital\Homeboxes\Model\ImageUploader;
use Digital\Homeboxes\Model\ResourceModel\Boxes\CollectionFactory;
use Magento\Framework\Registry;
use Magento\Ui\DataProvider\AbstractDataProvider;

class DataProvider extends AbstractDataProvider
{
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
        /** @var BoxesInterface | \Digital\Homeboxes\Model\Boxes $boxes */
        $boxes = $this->coreRegistry->registry('current_boxes');
        if ($boxes && $boxes->getId()) {
            $boxesData = $boxes->getData();
            foreach (ImageField::getField() as $field) {
                unset($boxesData[$field]);
                $imageName = $boxes->getData($field);
                if ($imageSrc = $boxes->getImageUrl($imageName)) {
                    try {
                        $size = $this->imageUploader->getSize($imageName);
                    } catch (\Exception $e) {
                        $size = 'undefined';
                    }
                    $boxesData[$field][] = [
                        'name' => $imageName,
                        'url' => $imageSrc,
                        'size' => $size,
                    ];
                }
            }
            $this->loadedData[$boxes->getId()] = $boxesData;
        } else {
            $this->loadedData = [];
        }

        return $this->loadedData;
    }
}
