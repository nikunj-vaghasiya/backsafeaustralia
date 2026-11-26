<?php
namespace Digital\Homeboxes\Model\ResourceModel;

use Magento\Framework\Model\AbstractModel;
use Magento\Framework\Model\ResourceModel\Db\AbstractDb;
use Magento\Framework\Model\ResourceModel\Db\Context;
use Magento\Framework\Stdlib\DateTime\DateTime;

class Boxes extends AbstractDb
{

    /**
     * @var DateTime
     */
    private $dateTime;

    /**
     * Boxes constructor.
     *
     * @param Context  $context
     * @param DateTime $dateTime
     * @param null     $connectionName
     */
    public function __construct(
        Context $context,
        DateTime $dateTime,
        $connectionName = null
    ) {
        parent::__construct($context, $connectionName);
        $this->dateTime = $dateTime;
    }

    /**
     * Initialize resource
     *
     * @return void
     */
    public function _construct()
    {
        $this->_init('digital_boxes', 'boxes_id');
    }

    /**
     * Before save
     *
     * @param AbstractModel $object
     * @return mixed
     */
    /* @codingStandardsIgnoreStart */
    protected function _beforeSave(AbstractModel $object)
    {
        return parent::_beforeSave($object);
    }
    /* @codingStandardsIgnoreEnd */

    /**
     * Get load
     *
     * @param field $field
     * @param value $value
     * @param object $object
     */
    protected function _getLoadSelect($field, $value, $object)
    {
        $select = parent::_getLoadSelect($field, $value, $object);

        if ($object->getStoreId()) {
            $storeIds = [\Magento\Store\Model\Store::DEFAULT_STORE_ID, (int) $object->getStoreId()];
            $select->join(
                ['digital_boxes_store' => $this->getTable('digital_boxes_store')],
                $this->getMainTable() . '.boxes_id = digital_boxes_store.boxes_id',
                []
            )->where(
                'status = ?',
                1
            )->where(
                'digital_boxes_store.store_id IN (?)',
                $storeIds
            )->order(
                'digital_boxes_store.store_id DESC'
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
            ['box' => $this->getMainTable()]
        )->join(
            ['box_store' => $this->getTable('digital_boxes_store')],
            'box.boxes_id = box_store.boxes_id',
            []
        )->where(
            'box_store.store_id IN (?)',
            $store
        );

        if ($isActive != null) {
            $select->where('box.status = ?', $isActive);
        }

        return $select;
    }

    /**
     * Get load
     *
     * @param storeid $storeId
     */
    public function checkIdentifier($storeId)
    {
        $stores = [\Magento\Store\Model\Store::DEFAULT_STORE_ID, $storeId];
        $select = $this->_getLoadByIdentifierSelect($stores, 1);
        $select->reset(\Magento\Framework\DB\Select::COLUMNS)
            ->columns('box.boxes_id')
            ->order('box_store.store_id DESC')
            ->limit(1);

        return $this->getConnection()->fetchOne($select);
    }

    /**
     * Before delete
     *
     * @param object $object
     */
    protected function _beforeDelete(\Magento\Framework\Model\AbstractModel $object)
    {
        $condition = ['boxes_id = ?' => (int) $object->getId()];
        $this->getConnection()->delete($this->getTable('digital_boxes_store'), $condition);

        $condition = ['boxes_id = ?' => (int) $object->getId()];
        $this->getConnection()->delete($this->getTable('digital_boxes_store'), $condition);

        return parent::_beforeDelete($object);
    }

    /**
     * After save
     *
     * @param object $object
     */
    protected function _afterSave(\Magento\Framework\Model\AbstractModel $object)
    {
        $oldStores = $this->lookupStoreIds($object->getId());
        $newStores = (array) $object->getStores();
        if (empty($newStores)) {
            $newStores = (array) $object->getStoreId();
        }
        $table = $this->getTable('digital_boxes_store');
        $insert = array_diff($newStores, $oldStores);
        $delete = array_diff($oldStores, $newStores);

        if ($delete) {
            $where = ['boxes_id = ?' => (int) $object->getId(), 'store_id IN (?)' => $delete];
            $this->getConnection()->delete($table, $where);
        }

        if ($insert) {
            $data = [];
            foreach ($insert as $storeId) {
                $data[] = ['boxes_id' => (int) $object->getId(), 'store_id' => (int) $storeId];
            }
            $this->getConnection()->insertMultiple($table, $data);
        }

        return parent::_afterSave($object);
    }

    /**
     * After load
     *
     * @param object $object
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
            $this->getTable('digital_boxes_store'),
            'store_id'
        )
            ->where(
                'boxes_id = ?',
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
