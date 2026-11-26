<?php

namespace Integration\Base\Model\Config\Source\Website;

class Selection implements \Magento\Framework\Option\ArrayInterface
{
    
    /**
     * @param \Magento\Framework\Locale\ListsInterface $localeLists
     */

    protected $_websiteCollectionFactory;
    
    public function __construct(\Magento\Store\Model\ResourceModel\Website\CollectionFactory $websiteCollectionFactory)
    {
        $this->_websiteCollectionFactory = $websiteCollectionFactory;
    }

    /**
     * Retrieve existing websites names of system
     *
     * @return Website Collection
     */
    public function getWebsiteLists()
    {
        $collection = $this->_websiteCollectionFactory->create();        
        $websiteName = [];
        
        foreach($collection as $website){
            $websiteName[] = ['label' => $website->getName(), 'value' => $website->getWebsiteId()];
        }               

        return $websiteName;
    }

    /**
     * {@inheritdoc}
     *
     * @codeCoverageIgnore
     */
    public function toOptionArray()
    {
        return $this->getWebsiteLists();
    }
}
