<?php
namespace Digital\Projects\Model\Projects;

use Digital\Projects\Api\Data\ProjectsInterface;
use Digital\Projects\Model\ImageField;
use Digital\Projects\Model\ImageUploader;
use Digital\Projects\Model\ResourceModel\Projects\CollectionFactory;
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
        /** @var ProjectsInterface | \Digital\Projects\Model\Projects $projects */
        $projects = $this->coreRegistry->registry('current_projects');
        if ($projects && $projects->getId()) {
            $projectsData = $projects->getData();
            foreach (ImageField::getField() as $field) {
                unset($projectsData[$field]);
                $imageName = $projects->getData($field);
                if ($imageSrc = $projects->getImageUrl($imageName)) {
                    try {
                        $size = $this->imageUploader->getSize($imageName);
                    } catch (\Exception $e) {
                        $size = 'undefined';
                    }
                    $projectsData[$field][] = [
                        'name' => $imageName,
                        'url' => $imageSrc,
                        'size' => $size,
                    ];
                }
            }
            $this->loadedData[$projects->getId()] = $projectsData;
        } else {
            $this->loadedData = [];
        }

        return $this->loadedData;
    }
}
