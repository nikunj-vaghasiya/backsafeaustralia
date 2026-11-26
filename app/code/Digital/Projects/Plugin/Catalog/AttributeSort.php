<?php
namespace Digital\Projects\Plugin\Catalog;

use Magento\Catalog\Model\ResourceModel\Product\Collection;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Registry;

/**
 * Class AttributeSort
 * @package Mageplaza\Blog\Plugin\Catalog
 */
class AttributeSort
{
    /**
     * @var Data
     */
    protected $helper;

    /**
     * @var RequestInterface
     */
    private $request;

    /**
     * @var Registry
     */
    public $coreRegistry;

    /**
     * Topmenu constructor.
     *
     * @param RequestInterface $request
     * @param Registry $coreRegistry
     * @param Data $helper
     */
    public function __construct(
        RequestInterface $request,
        Registry $coreRegistry
    ) {
        $this->request      = $request;
        $this->coreRegistry = $coreRegistry;
    }

    /**
     * @param Collection $productCollection
     * @param callable $proceed
     * @param $attribute
     * @param $dir
     *
     * @return Collection
     */
    public function aroundAddAttributeToSort(
        Collection $productCollection,
        callable $proceed,
        $attribute,
        $dir = 'ASC'
    ) {
        if (in_array(
            $this->request->getFullActionName(),
            ['projects_projects_edit'],
            true
        )) {
            $post_id = $this->getPostId();

            /*if ($post_id && $attribute === 'position') {
                $productCollection->getSelect()->where('mp_p.post_id = ' . $post_id);
            } else {
                $productCollection->getSelect()->group('e.entity_id');
            }

            if ($attribute === 'position') {
                $productCollection->getSelect()->order('position ' . $dir);

                return $productCollection;
            }
            return $productCollection->getSelect()->order('entity_id ' . $dir);*/
        }

        return $proceed($attribute, $dir);
    }

    /**
     * @param Collection $productCollection
     * @param callable $proceed
     * @param $attribute
     * @param $condition
     * @param $joinType
     *
     * @return Collection
     */
    public function aroundAddAttributeToFilter(
        Collection $productCollection,
        callable $proceed,
        $attribute,
        $condition = null,
        $joinType = 'inner'
    ) {
        if (in_array(
            $this->request->getFullActionName(),
            ['projects_projects_edit'],
            true
        )) {
            $post_id = $this->getPostId();
            if ($post_id && $attribute === 'position') {
                $productCollection->getSelect()->where('mp_p.post_id = ' . $post_id);
            } else {
                $productCollection->getSelect()->group('e.entity_id');
            }

            
        }

        return $proceed($attribute, $condition, $joinType);
    }

    /**
     * @return string
     */
    public function getPostId()
    {
        if ($this->request->getParam('filter') === '') {
            return null;
        }
        $post = $this->coreRegistry->registry('digital_projects');

        return $this->request->getParam('id');
    }
}
