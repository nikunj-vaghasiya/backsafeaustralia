<?php
namespace Integration\ManualSync\Model;

class Watch extends \Magento\Framework\Model\AbstractModel
{
    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Integration\ManualSync\Model\ResourceModel\Watch');
    }

    public function getLastRunningRecord($type,$status,$websiteId)
    {
    	$this->_getResource()->getLastRunningRecord($this, $type, $status, $websiteId);
        return $this;
    }
}
