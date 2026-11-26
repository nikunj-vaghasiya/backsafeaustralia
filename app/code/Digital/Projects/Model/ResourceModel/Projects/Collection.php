<?php
namespace Digital\Projects\Model\ResourceModel\Projects;

use Digital\Projects\Model\Projects;
use Digital\Projects\Model\ResourceModel\Projects as ProjectsResourceModel;
use Digital\Base\Model\Source\Status;
use Digital\Projects\Model\ResourceModel\AbstractCollection;

class Collection extends AbstractCollection
{
    /**
     * Primary column
     *
     * @var string
     */
    protected $_idFieldName = 'projects_id';

    protected $_previewFlag;

    /**
     * Initialize resource collection
     *
     * @return void
     */
    public function _construct()
    {
        $this->_init(Projects::class, projectsResourceModel::class);
        $this->_map['fields']['projects_id'] = 'main_table.projects_id';
        $this->_map['fields']['store'] = 'store_table.store_id';
    }

    /**
     * Only get enable projects
     *
     * @return Collection
     */
    public function addActiveStatusFilter()
    {
        return $this->addFieldToFilter('status', Status::STATUS_ENABLED);
    }

    /**
     * Set first store flag
     *
     * @param bool $flag
     * @return $this
     */
    public function setFirstStoreFlag($flag = false)
    {
        $this->_previewFlag = $flag;
        return $this;
    }

    /**
     * Add filter by store
     *
     * @param int|array|\Magento\Store\Model\Store $store
     * @param bool $withAdmin
     * @return $this
     */
    public function addStoreFilter($store, $withAdmin = true)
    {
        if (!$this->getFlag('store_filter_added')) {
            $this->performAddStoreFilter($store, $withAdmin);
        }
        return $this;
    }

    /**
     * Perform operations after collection load
     *
     * @return $this
     */
    protected function _afterLoad()
    {
        $this->performAfterLoad('digital_projects_store', 'projects_id');
        $this->_previewFlag = false;

        return parent::_afterLoad();
    }

    /**
     * Perform operations before rendering filters
     *
     * @return void
     */
    protected function _renderFiltersBefore()
    {
        $this->joinStoreRelationTable('digital_projects_store', 'projects_id');
    }
}
