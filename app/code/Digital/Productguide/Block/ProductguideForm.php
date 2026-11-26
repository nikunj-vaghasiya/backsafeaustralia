<?php
namespace Digital\Productguide\Block;

/**
 * Services content block
 */
class ProductguideForm extends \Magento\Framework\View\Element\Template
{
    /**
     * @var formKey
     */
    protected $formKey;

    /**
     * @var coreSession
     */
    protected $_coreSession;

    /**
     * Get StoreId
     *
     * @return void
     */
    public function getStoreId()
    {
        return $this->_storeManager->getStore()->getId();
    }

    /**
     * Returns action url for contact form
     *
     * @return string
     */
    public function getFormAction()
    {
        return $this->getUrl('productguide/index/post', ['_secure' => true]);
    }
}
