<?php
namespace Digital\Projects\Block;

use Digital\Projects\Model\ResourceModel\Projects\CollectionFactory as ProjectsCollectionFactory;
use Magento\Framework\View\Element\Template as Template;
use Magento\Framework\View\Page\Config as PageConfig;

abstract class AbstractProjects extends Template
{
    /**
     * @var ProjectsCollectionFactory
     */
    protected $projectsCollectionFactory;

    /**
     * @var Collection
     */
    protected $projectsCollection;

    protected $cookathomeoptions;
    protected $cookemupcampingprojectsoptions;
    protected $christmasprojectsoptions;
    protected $winterwarmerprojectsoptions;
    protected $categoriesprojectsoptions;
    protected $proteinprojectsoptions;
    protected $urlInterface;
    protected $pageConfig;

    /**
     * AbstractSlider constructor.
     *
     * @param Template\Context $context
     * @param ProjectsCollectionFactory $projectsCollectionFactory
     * @param Basehelper $baseHelper
     * @param Projectshelper $projectsHelper
     * @param array $data
     */

    public function __construct(
        Template\Context $context,
        ProjectsCollectionFactory $projectsCollectionFactory,
        \Digital\Base\Helper\Data $baseHelper,
        \Digital\Projects\Helper\Data $projectsHelper,
        \Digital\Projects\Model\Source\Cookathomeoptions $cookathomeoptions,
        \Digital\Projects\Model\Source\Cookemupprojectsoptions $cookemupcampingprojectsoptions,
        \Digital\Projects\Model\Source\Christmasprojectsoptions $christmasprojectsoptions,
        \Digital\Projects\Model\Source\Winterwarmerprojectsoptions $winterwarmerprojectsoptions,
        \Digital\Projects\Model\Source\Categoriesprojectsoptions $categoriesprojectsoptions,
        \Digital\Projects\Model\Source\Proteinprojectsoptions $proteinprojectsoptions,
        \Magento\Framework\UrlInterface $urlInterface,
        PageConfig $pageConfig,
        array $data = []
    ) {
        
        $this->projectsCollectionFactory = $projectsCollectionFactory;
        $this->baseHelper = $baseHelper;
        $this->projectsHelper = $projectsHelper;
        $this->cookathomeoptions = $cookathomeoptions;
        $this->cookemupcampingprojectsoptions = $cookemupcampingprojectsoptions;
        $this->christmasprojectsoptions = $christmasprojectsoptions;
        $this->winterwarmerprojectsoptions = $winterwarmerprojectsoptions;
        $this->categoriesprojectsoptions = $categoriesprojectsoptions;
        $this->proteinprojectsoptions = $proteinprojectsoptions;
        $this->urlInterface = $urlInterface;
        $this->pageConfig = $pageConfig;
        parent::__construct($context, $data);
    }

    protected function _prepareLayout()
    {
        parent::_prepareLayout();
        
        $canonicalUrl = $this->getCurrentUrl();

        $this->pageConfig->addRemotePageAsset(
            $canonicalUrl,
            'canonical',
            ['attributes' => ['rel' => 'canonical']]
        );

        if ($this->getProjectsCollection()) {
            $pager = $this->getLayout()->createBlock(
                \Magento\Theme\Block\Html\Pager::class,
                'project.projects.pager'
            )->setAvailableLimit([$this->getDefaultPageSize() => $this->getDefaultPageSize()])
                ->setShowPerPage(true)->setCollection(
                    $this->getProjectsCollection()
                );
            $this->setChild('pager', $pager);
            $this->getProjectsCollection()->load();
        }
        return $this;
    }
    public function getPagerHtml()
    {
        return $this->getChildHtml('pager');
    }

    /**
     * Get projects collection
     *
     * @return boolean|Collection
     */
    public function getProjectsCollection()
    {

        if ($this->projectsCollection === null) {
            $page=($this->getRequest()->getParam('p'))? $this->getRequest()->getParam('p') : 1;
            $pageSize=($this->getRequest()->getParam('limit'))?
            $this->getRequest()->getParam('limit') : $this->getDefaultPageSize();
            $projectsCollection = $this->projectsCollectionFactory->create();
            $projectsCollection->setOrder('sort_order', 'ASC');
            $projectsCollection->addActiveStatusFilter();
            $projectsCollection->setCurPage($page);
            $projectsCollection->setPageSize($pageSize);
            $this->projectsCollection = $projectsCollection;
        }

        return $this->projectsCollection;
    }

    public function getHomeProjectsCollection() {
        $projectsCollection = $this->projectsCollectionFactory->create();
        $projectsCollection->addFieldToFilter('status', "1");
        $projectsCollection->setOrder('sort_order', "ASC");
        $projectsCollection->addFieldToFilter('show_on_homepage', "1");
       
        return $projectsCollection;
    }

    public function getCurrentUrl() {
        return $this->urlInterface->getUrl().'projects';
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

    /* show per page recors in frontend*/
    public function getDefaultPageSize()
    {
        return $this->projectsHelper->getProjectsCollectionLimit();
    }
    
}
