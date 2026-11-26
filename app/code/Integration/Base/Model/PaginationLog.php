<?php
namespace Integration\Base\Model;

class PaginationLog extends \Integration\Base\Model\AbstractModel
{
    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Integration\Base\Model\ResourceModel\PaginationLog');
    }

    public function checkLogStatus($logTypeId,$mode = \Integration\ManualSync\Helper\Data::AUTO_MODE, $status = 1)
    {
        $this->_getResource()->checkLogStatus($this, $logTypeId,$mode,$status);
        return $this;
    }

    public function updatePaginationLog($logTypeId, $params = [])
    {
    	try {
    		$this->_getResource()->updatePaginationLog($this, $logTypeId, $params);
    	} catch (\Exception $e) {
    		throw $e;
    	}

        return $this;	
    }
}
?>
