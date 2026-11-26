<?php
namespace Integration\Product\Model;

class Product extends \Integration\Base\Model\AbstractModel
{
    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Integration\Product\Model\ResourceModel\Product');
    }    

    /**
     * Load product by sku
     *
     * @param   string $sku
     * @param   int $websiteId
     * @return  $this
     */
    public function loadProductBySku($sku, $websiteId)
    {
        $this->_getResource()->loadProductBySku($this, $sku, $websiteId);
        return $this;
    }
}
?>
