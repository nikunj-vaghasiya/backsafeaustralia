<?php
namespace Integration\Cron\Model;

class Buffer extends \Integration\Base\Model\AbstractModel
{
	/**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Integration\Cron\Model\ResourceModel\Buffer');
    }

    /**
     * Load cron buffer record by type, status and website id
     *
     * @param   string $type
     * @param   string $status
     * @param   int $websiteId
     * @return  $this
     */
    public function getBatchRecord($type,$mode,$status,$websiteId,$flag = 1)
    {
    	$this->_getResource()->getBatchRecord($this, $type, $mode, $status, $websiteId, $flag);
        return $this;
    }
}
?>
