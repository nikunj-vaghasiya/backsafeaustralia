<?php
namespace Integration\Cron\Model\ResourceModel;

class Buffer extends \Integration\Base\Model\ResourceModel\AbstractResource
{
	/**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('integration_cron_buffer', 'api_entity_id');
    }

    /**
     * Load customer by account number
     *
     * @param \Integration\Customer\Model\Customer $apiCustomer
     * @param string $accountNumber
     * @return $this
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getBatchRecord(\Integration\Cron\Model\Buffer $cronBuffer, $type, $mode, $status, $websiteId, $flag = 1)
    {
        $connection = $this->getConnection();
        $bind = ['type' => $type,'status' => $status, 'mode' => $mode, 'new_flag' => $flag];

        $select = $connection->select()->from(
            $this->getMainTable(),
            [$this->getIdFieldName()]
        )->where(
            'type = :type'
        )->where(
            'status = :status'
        )->where(
            'mode = :mode'
        )->where(
            'new_flag = :new_flag'
        );

        if(!$websiteId){
            $websiteId = 1; /* Main Website */
            if($cronBuffer->hasData('website_id')) {
                $websiteId = trim($cronBuffer->getWebsiteId());
            }
        }

        $bind['website_id'] = $websiteId;
        $select->where('website_id = :website_id');

        $cronBufferId = $connection->fetchOne($select, $bind);
        if ($cronBufferId) {
            $this->load($cronBuffer, $cronBufferId);
        }
        return $this;
    }
}
?>
