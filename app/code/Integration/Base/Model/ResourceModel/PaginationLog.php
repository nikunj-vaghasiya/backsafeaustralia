<?php

namespace Integration\Base\Model\ResourceModel;

class PaginationLog extends \Integration\Base\Model\ResourceModel\AbstractResource
{
	/**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('integration_pagination_log', 'api_entity_id');
    }

    /**
     * Check entity scope, confirmation key before saving
     *
     * @param \Magento\Framework\DataObject|\Integration\Base\Model\PaginationLog $apiPaginationLog
     *
     * @return $this
     */
    /*protected function _beforeSave(\Magento\Framework\Model\AbstractModel $apiPaginationLog)
    {
    	if (!$apiPaginationLog->getWebsiteId()) {
            $apiPaginationLog->setWebsiteId($this->storeManager->getStore()->getWebsiteId());
        }

        parent::_beforeSave($apiPaginationLog);

        return $this;
    }*/

    public function checkLogStatus(\Integration\Base\Model\PaginationLog $paginationLog,$logTypeId,$mode,$status)
    {
        $connection = $this->getConnection();
        $bind = ['log_type' => $logTypeId, 'status' => $status];

        $select = $connection->select()->from(
            $this->getMainTable(),
            [$this->getIdFieldName()]
        )->where(
            'log_type = :log_type'
        );

        if($mode == \Integration\ManualSync\Helper\Data::MANUAL_MODE) {
            $select->where('manual_status = :status');
        } else {
            $select->where('auto_status = :status');
        }

        $paginationLogId = $connection->fetchOne($select, $bind);
        if ($paginationLogId) {
            $this->load($paginationLog, $paginationLogId);
        } else {
            $paginationLog->setData([]);
        }

        return $this;
    }

    public function updatePaginationLog(\Integration\Base\Model\PaginationLog $paginationLog, $logTypedId, $params = [])
    {
        $connection = $this->getConnection();
        $bind = ['log_type' => $logTypedId];

        $select = $connection->select()->from(
            $this->getMainTable(),
            [$this->getIdFieldName()]
        )->where(
            'log_type = :log_type'
        );

        $paginationLogId = $connection->fetchOne($select, $bind);
        if ($paginationLogId) {
            $this->load($paginationLog, $paginationLogId);
        } else {
            $paginationLog->setData([]);
        }

        if(is_array($params) && !empty($params)) {
            $this->getConnection()->beginTransaction();
            /* TO DO in future this should be optimize */
            $paginationLog->setData($params);
            $paginationLog->setId($paginationLogId);
            $fields = array_keys($params);
            $data = array_intersect_key($paginationLog->getData(), array_flip($fields));
            /* TO DO in future this should be optimize */
            try {
                if ($paginationLog->getId() && !empty($data)) {
                    $this->getConnection()->update(
                        $this->getMainTable(),
                        $data,
                        [$this->getIdFieldName() . '= ?' => (int)$paginationLog->getId()]
                    );
                    $paginationLog->addData($data);
                }
                $this->getConnection()->commit();
            } catch (\Exception $e) {
                $this->getConnection()->rollBack();
                throw $e;
            }
        }

        return $this;
    }
}
