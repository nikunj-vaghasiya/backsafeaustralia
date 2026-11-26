<?php
namespace Digital\Projects\Block;

use Digital\Base\Helper\Data as baseHelper;
use Digital\Projects\Block\AbstractProjects;
use Digital\Projects\Helper\Data as projectsHelper;
use Digital\Projects\Model\ResourceModel\Projects\CollectionFactory as ProjectsCollectionFactory;
use Magento\Framework\View\Element\Template as Template;

class Projects extends AbstractProjects
{
    /**
     * Slider constructor.
     *
     * @param Template\Context        $context
     * @param ProjectsCollectionFactory  $projectsCollectionFactory
     * @param Basehelper              $baseHelper
     * @param Projectshelper          $projectsHelper
     * @param array                   $data
     */

    /* @codingStandardsIgnoreStart */
    public function __construct(
        Template\Context $context,
        ProjectsCollectionFactory $projectsCollectionFactory,
        baseHelper $baseHelper,
        projectsHelper $projectsHelper,
        \Digital\Projects\Model\Source\Cookathomeoptions $cookathomeoptions,
        \Digital\Projects\Model\Source\Cookemupprojectsoptions $cookemupcampingprojectsoptions,
        \Digital\Projects\Model\Source\Christmasprojectsoptions $christmasprojectsoptions,
        \Digital\Projects\Model\Source\Winterwarmerprojectsoptions $winterwarmerprojectsoptions,
        \Digital\Projects\Model\Source\Categoriesprojectsoptions $categoriesprojectsoptions,
        \Digital\Projects\Model\Source\Proteinprojectsoptions $proteinprojectsoptions,
        \Magento\Framework\UrlInterface $urlInterface,
        \Magento\Framework\View\Page\Config $pageConfig,
        array $data = []
    ) {
        parent::__construct(
            $context,
            $projectsCollectionFactory,
            $baseHelper,
            $projectsHelper,
            $cookathomeoptions,
            $cookemupcampingprojectsoptions,
            $christmasprojectsoptions,
            $winterwarmerprojectsoptions,
            $categoriesprojectsoptions,
            $proteinprojectsoptions,
            $urlInterface,
            $pageConfig,
            $data
        );
    }
    /* @codingStandardsIgnoreEnd */
}
