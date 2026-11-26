<?php
namespace Digital\Projects\Block;

use Digital\Projects\Model\ResourceModel\Projects\CollectionFactory as ProjectsCollectionFactory;
use Magento\Framework\View\Element\Template as Template;
use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory;
use Magento\Catalog\Block\Product\ListProduct;
use Magento\Catalog\Block\Product\Context;
use Magento\Framework\UrlInterface;

class Projectsview extends \Magento\Catalog\Block\Product\AbstractProduct
{
    /**
     * @var ProjectsCollectionFactory
     */
    protected $projectsCollectionFactory = null;

    /**
     * @var Collection
     */
    protected $projectsCollection;
    protected $templateProcessor;
    protected $proCollectionFactory;
    protected $productFactory;
    protected $listProduct;
    protected $storeManager;
    protected $urlInterface;

    /**
     * AbstractSlider constructor.
     *
     * @param Template\Context        $context
     * @param ProjectsCollectionFactory  $projectsCollectionFactory
     * @param Basehelper              $baseHelper
     * @param Projectshelper         $projectsHelper
     * @param array                   $data
     */
    public function __construct(
        Context $context,
        ProjectsCollectionFactory $projectsCollectionFactory,
        \Digital\Base\Helper\Data $baseHelper,
        \Digital\Projects\Helper\Data $projectsHelper,
        \Digital\Projects\Model\ResourceModel\Products\CollectionFactory $proCollectionFactory,
        \Magento\Widget\Model\Template\Filter $templateProcessor,
        CollectionFactory $productFactory,
        ListProduct $listProduct,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        UrlInterface $urlInterface,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->projectsCollectionFactory = $projectsCollectionFactory;
        $this->baseHelper = $baseHelper;
        $this->projectsHelper = $projectsHelper;
        $this->proCollectionFactory = $proCollectionFactory;
        $this->templateProcessor = $templateProcessor;
        $this->productFactory   = $productFactory;
        $this->listProduct = $listProduct;
        $this->storeManager = $storeManager;
        $this->urlInterface = $urlInterface;
    }

    protected function _prepareLayout()
    {
        $currentUrl = strtok($this->urlInterface->getCurrentUrl(), '?');

        $this->pageConfig->addRemotePageAsset(
            $currentUrl,
            'canonical',
            [
                'attributes' => [
                    'rel' => 'canonical'
                ]
            ]
        );

        return parent::_prepareLayout();
    }

    public function getImageFullPath($image_desktop)
    {
        return $this ->storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA).'digital/projects/'.$image_desktop;
    }

    public function getProductCollection()
    {
        $productProjects = $this->getProjectsProductsCollection()->getData();
        $products = array();
        if (count($productProjects) > 0) {
            foreach($productProjects as $itemProjects)
            {
                $products[] = $itemProjects['product_id'];
            }
        }        
        
        $productCollection = $this->productFactory->create();
        $productCollection->addFieldToFilter('entity_id', array('in' => $products))
                          ->addAttributeToSelect(['entity_id','id','url_key','image','name','price','special_price','status','small_image','thumbnail','visibility','updated_at'])
                          ->addAttributeToFilter('visibility', \Magento\Catalog\Model\Product\Visibility::VISIBILITY_BOTH)
                          ->addAttributeToFilter('status', \Magento\Catalog\Model\Product\Attribute\Source\Status::STATUS_ENABLED)
                          ->setPageSize(5)
                          ->setOrder('entity_id', 'DESC');
        return $productCollection;
    }
    
    

    /**
     * Get projects collection
     *
     * @return boolean|Collection
     */
    public function getProjectsCollection()
    {
        $viewCollection = $this->projectsCollectionFactory ->create();
        $viewCollection->addFieldToSelect('*')->addFieldToFilter("projects_id", $this->getProjectsId())->getFirstItem();
        return $viewCollection;
    }


    public function getProjectsProductsCollection()
    {
        $prodCollection = $this->proCollectionFactory ->create();
        $prodCollection->addFieldToSelect('*')->addFieldToFilter("projects_id", $this->getProjectsId());
        return $prodCollection;
    }

    /**
     * Get Projects ID
     *
     * @return int
     */
    public function getProjectsId()
    {
        return $this->getRequest()->getParam('id');
    }

    /**
     * To Html
     *
     * @return string
     */
    public function getBaseHelper()
    {
        return $this->baseHelper;
    }

    /**
     * To Html
     *
     * @return string
     */
    public function getProjectsHelper()
    {
        return $this->projectsHelper;
    }

    public function filterOutputHtml($string) 
    {
        return $this->templateProcessor->filter($string);
    }
}
