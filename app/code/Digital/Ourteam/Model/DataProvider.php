<?php
namespace Digital\Ourteam\Model;

use Digital\Ourteam\Api\Data\OurteamInterface;
use Digital\Ourteam\Model\ImageField;
use Digital\Ourteam\Model\ImageUploader;
use Digital\Ourteam\Model\ResourceModel\Ourteam\Collection;
use Digital\Ourteam\Model\ResourceModel\Ourteam\CollectionFactory;
use Magento\Framework\Registry;
use Magento\Ui\DataProvider\AbstractDataProvider;

class DataProvider extends \Magento\Ui\DataProvider\AbstractDataProvider
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
        /** @var OurteamInterface | \Digital\Ourteam\Model\Ourteam $team */
        $team = $this->coreRegistry->registry('current_team');

        if ($team && $team->getTeamId()) {
            $teamData = $team->getData();
            foreach (ImageField::getField() as $field) {
                unset($teamData[$field]);
                $imageName = $team->getData($field);
                if ($imageSrc = $team->getImageUrl($imageName)) {
                    try {
                        $size = $this->imageUploader->getSize($imageName);
                    } catch (\Exception $e) {
                        $size = 'undefined';
                    }
                    $teamData[$field][] = [
                        'name' => $imageName,
                        'url' => $imageSrc,
                        'size' => $size,
                    ];
                }
            }
            $this->loadedData[$team->getTeamId()] = $teamData;
        } else {
            $this->loadedData = [];
        }

        return $this->loadedData;
    }
}