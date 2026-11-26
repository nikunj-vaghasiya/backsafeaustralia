<?php
namespace Digital\BannerSlider\Model\ResourceModel;

use Magento\Framework\Model\AbstractModel;
use Magento\Framework\Model\ResourceModel\Db\AbstractDb;
use Magento\Framework\Model\ResourceModel\Db\Context;
use Magento\Framework\Stdlib\DateTime\DateTime;

class Banner extends AbstractDb
{

    /**
     * @var DateTime
     */
    private $dateTime;
    
    private $_storeManager;

    /**
     * Banner constructor.
     *
     * @param Context      $context
     * @param DateTime     $dateTime
     * @param StoreManager $storeManager
     * @param null         $connectionName
     */
    public function __construct(
        Context $context,
        DateTime $dateTime,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        $connectionName = null
    ) {
        parent::__construct($context, $connectionName);
        $this->_storeManager = $storeManager;
        $this->dateTime = $dateTime;
    }

    /**
     * Initialize resource
     *
     * @return void
     */
    public function _construct()
    {
        $this->_init('digital_banner', 'banner_id');
    }

    /**
     * Before save
     *
     * @param AbstractModel $object
     * @return mixed
     */
    /*@codingStandardsIgnoreStart*/
    protected function _beforeSave(AbstractModel $object)
    {
        return parent::_beforeSave($object);
    }
    /*@codingStandardsIgnoreEnd*/

    /**
     * GetLoadSelect
     *
     * @param string $field
     * @param string $value
     * @param string $object
     * @return mixed
     */
    protected function _getLoadSelect($field, $value, $object)
    {
        $select = parent::_getLoadSelect($field, $value, $object);

        if ($object->getStoreId()) {
            $storeIds = [\Magento\Store\Model\Store::DEFAULT_STORE_ID, (int) $object->getStoreId()];
            $select->join(
                ['digital_banner_store' => $this->getTable('digital_banner_store')],
                $this->getMainTable() . '.banner_id = digital_banner_store.banner_id',
                []
            )->where(
                'status = ?',
                1
            )->where(
                'digital_banner_store.store_id IN (?)',
                $storeIds
            )->order(
                'digital_banner_store.store_id DESC'
            )->limit(1);
        }
        return $select;
    }

    /**
     * Retrieve load select with filter by identifier, store and activity
     *
     * @param string $identifier
     * @param int|array $store
     * @param int $isActive
     * @return \Magento\Framework\DB\Select
     */
    protected function _getLoadByIdentifierSelect($identifier, $store, $isActive = null)
    {
        $select = $this->getConnection()->select()->from(
            ['gm' => $this->getMainTable()]
        )->join(
            ['gms' => $this->getTable('digital_banner_store')],
            'gm.banner_id = gms.banner_id',
            []
        )->where(
            'gms.store_id IN (?)',
            $store
        );

        if ($isActive != null) {
            $select->where('gm.status = ?', $isActive);
        }

        return $select;
    }

    /**
     * Check by identifier and store
     *
     * @param string $storeId
     */
    public function checkIdentifier($storeId)
    {
        $stores = [\Magento\Store\Model\Store::DEFAULT_STORE_ID, $storeId];
        $select = $this->_getLoadByIdentifierSelect($stores, 1);
        $select->reset(\Magento\Framework\DB\Select::COLUMNS)
            ->columns('gm.banner_id')
            ->order('gms.store_id DESC')
            ->limit(1);
        return $this->getConnection()->fetchOne($select);
    }

    /**
     * Before Delete
     *
     * @param string $object
     */
    protected function _beforeDelete(\Magento\Framework\Model\AbstractModel $object)
    {
        $condition = ['banner_id = ?' => (int) $object->getId()];
        $this->getConnection()->delete($this->getTable('digital_banner_store'), $condition);

        $condition = ['banner_id = ?' => (int) $object->getId()];
        $this->getConnection()->delete($this->getTable('digital_banner_store'), $condition);

        return parent::_beforeDelete($object);
    }

    /**
     * After Delete
     *
     * @param string $object
     */
    protected function _afterSave(\Magento\Framework\Model\AbstractModel $object)
    {
        $oldStores = $this->lookupStoreIds($object->getId());
        $newStores = (array) $object->getStores();
        if (empty($newStores)) {
            $newStores = (array) $object->getStoreId();
        }
        $table = $this->getTable('digital_banner_store');
        $insert = array_diff($newStores, $oldStores);
        $delete = array_diff($oldStores, $newStores);

        if ($delete) {
            $where = ['banner_id = ?' => (int) $object->getId(), 'store_id IN (?)' => $delete];
            $this->getConnection()->delete($table, $where);
        }

        if ($insert) {
            $data = [];
            foreach ($insert as $storeId) {
                $data[] = ['banner_id' => (int) $object->getId(), 'store_id' => (int) $storeId];
            }
            $this->getConnection()->insertMultiple($table, $data);
        }

        return parent::_afterSave($object);
    }

    /**
     * After Load
     *
     * @param string $object
     */
    protected function _afterLoad(\Magento\Framework\Model\AbstractModel $object)
    {
        if ($object->getId()) {
            $stores = $this->lookupStoreIds($object->getId());
            $object->setData('store_id', $stores);
        }

        return parent::_afterLoad($object);
    }

    /**
     * Get store ids to which specified item is assigned
     *
     * @param int $companyId
     * @return array
     */
    public function lookupStoreIds($companyId)
    {
        $connection = $this->getConnection();

        $select = $connection->select()->from(
            $this->getTable('digital_banner_store'),
            'store_id'
        )
            ->where(
                'banner_id = ?',
                (int) $companyId
            );
        return $connection->fetchCol($select);
    }

    /**
     * Set store model
     *
     * @param Store $store
     * @return $this
     */
    public function setStore($store)
    {
        $this->_store = $store;
        return $this;
    }

    /**
     * Retrieve store model
     *
     * @return Store
     */
    public function getStore()
    {
        return $this->_storeManager->getStore($this->_store);
    }
}
