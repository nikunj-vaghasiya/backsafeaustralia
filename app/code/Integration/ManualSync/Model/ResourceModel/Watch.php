<?php
namespace Integration\ManualSync\Model\ResourceModel;

class Watch extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('integration_manualsync_watch', 'api_entity_id');
    }

    /**
     * Load customer by account number
     *
     * @param \Integration\Customer\Model\Customer $apiCustomer
     * @param string $accountNumber
     * @return $this
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getLastRunningRecord(\Integration\ManualSync\Model\Watch $manualSyncWatch, $type, $status, $websiteId)
    {
        $connection = $this->getConnection();
        $bind = ['type' => $type,'status' => $status];

        $select = $connection->select()->from(
            $this->getMainTable(),
            [$this->getIdFieldName()]
        )->where(
            'type = :type'
        )->where(
            'status = :status'
        );

        if(!$websiteId){
            $websiteId = 1; /* Main Website */
            if($manualSyncWatch->hasData('website_id')) {
                $websiteId = trim($manualSyncWatch->getWebsiteId());
            }
        }

        $bind['website_id'] = $websiteId;
        $select->where('website_id = :website_id');

        $manualSyncWatchId = $connection->fetchOne($select, $bind);
        if ($manualSyncWatchId) {
            $this->load($manualSyncWatch, $manualSyncWatchId);
        }/* else {
            $manualSyncWatch->setData([]);
        }*/
        return $this;
    }
}
?>
