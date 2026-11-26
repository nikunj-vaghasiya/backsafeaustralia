<?php

namespace Integration\Product\Model\ResourceModel;

class Product extends \Integration\Base\Model\ResourceModel\AbstractResource
{
	/**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('integration_product', 'api_entity_id');
    }

    /**
     * Check product scope, confirmation key before saving
     *
     * @param \Magento\Framework\DataObject|\Integration\Product\Model\Product $apiProduct
     *
     * @return $this
     */
    protected function _beforeSave(\Magento\Framework\Model\AbstractModel $apiProduct)
    {
    	if (!$apiProduct->getWebsiteId()) {
            $apiProduct->setWebsiteId($this->storeManager->getStore()->getWebsiteId());
        }

        parent::_beforeSave($apiProduct);

        return $this;
    }

    /**
     * Load product by sku
     *
     * @param \Integration\Product\Model\Product $apiProduct
     * @param string $sku
     * @param int $websiteId
     * @return $this
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function loadProductBySku(\Integration\Product\Model\Product $apiProduct, $sku, $websiteId = 1)
    {
        $connection = $this->getConnection();
        $bind = ['sku' => $sku];

        $select = $connection->select()->from(
            $this->getMainTable(),
            [$this->getIdFieldName()]
        )->where(
            'sku = :sku'
        );

        $bind['website_id'] = $websiteId;
        $select->where('website_id = :website_id');

        $apiProductId = $connection->fetchOne($select, $bind);
        if ($apiProductId) {
            $this->load($apiProduct, $apiProductId);
        } else {
            $apiProduct->setData([]);
        }

        return $this;
    }
}
