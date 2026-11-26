<?php
namespace Digital\BannerSlider\Model\ResourceModel\Banner;

use Digital\BannerSlider\Model\Banner;
use Digital\BannerSlider\Model\ResourceModel\AbstractCollection;
use Digital\BannerSlider\Model\ResourceModel\Banner as BannerResourceModel;
use Digital\BannerSlider\Model\Source\Status;

class Collection extends AbstractCollection
{
    /**
     * Primary column
     *
     * @var string
     */
    protected $_idFieldName = 'banner_id';
    
    protected $_previewFlag;

    /**
     * Initialize resource collection
     *
     * @return void
     */
    public function _construct()
    {
        $this->_init(Banner::class, BannerResourceModel::class);
        $this->_map['fields']['banner_id'] = 'main_table.banner_id';
        $this->_map['fields']['store'] = 'store_table.store_id';
    }

    /**
     * Only get enable banner
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
        $this->performAfterLoad('digital_banner_store', 'banner_id');
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
        $this->joinStoreRelationTable('digital_banner_store', 'banner_id');
    }
}
