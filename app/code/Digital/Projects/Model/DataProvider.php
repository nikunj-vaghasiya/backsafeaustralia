<?php
namespace Digital\Projects\Model;

use Digital\Projects\Model\ResourceModel\Projects\CollectionFactory;
use Magento\Store\Model\StoreManagerInterface;
class DataProvider extends \Magento\Ui\DataProvider\AbstractDataProvider
{
    /**
     * @var array
     */
    protected $loadedData;

    protected $collection;
    protected $storeManager;

    // @codingStandardsIgnoreStart
    public function __construct(
        $name,
        $primaryFieldName,
        $requestFieldName,
        CollectionFactory $collectionFactory,
        StoreManagerInterface $storeManager,
        array $meta = [],
        array $data = []
    ) {
        $this->collection = $collectionFactory->create();
        $this->storeManager = $storeManager;
        parent::__construct($name, $primaryFieldName, $requestFieldName, $meta, $data);
    }
    // @codingStandardsIgnoreEnd
    
    public function getData()
    {
        if (isset($this->loadedData)) {
            return $this->loadedData;
        }

        $imageUrl = $this->storeManager->getStore()->getBaseUrl(
                        \Magento\Framework\UrlInterface::URL_TYPE_MEDIA);    
        $items = $this->collection->getItems();
        foreach ($items as $projects) { 
            $itemData = $projects->getData();
            if(isset($itemData['list_image'])) {
                $image = [];
                $image['list_image'][0]['name'] = $itemData['list_image'];
                $image['list_image'][0]['url'] = $imageUrl.'projects/image/'.$itemData['list_image'];
                $image['list_image'][0]['type'] = 'image';
                $itemData['list_image'] = $image;
                $fullData = $this->loadedData;

                /*$this->loadedData[$projects->getProjectsId()]['general_information'] = array_merge($fullData[$projects->getProjectsId()], $image);*/
                
            }      
            $this->loadedData[$projects->getProjectsId()] = $projects->getData();
        }
        return $this->loadedData;
    }
}