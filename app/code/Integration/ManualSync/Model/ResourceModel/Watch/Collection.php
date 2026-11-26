<?php
namespace Integration\ManualSync\Model\ResourceModel\Watch;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    /**
     * Define resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Integration\ManualSync\Model\Watch', 'Integration\ManualSync\Model\ResourceModel\Watch');
    }
}
?>
