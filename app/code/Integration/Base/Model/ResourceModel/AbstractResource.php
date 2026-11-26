<?php
namespace Integration\Base\Model\ResourceModel;

class AbstractResource extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
	/**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;
    
	/**
     * Constructor
     *
     * @param \Magento\Framework\Model\ResourceModel\Db\Context $context
     * @param string $connectionName
     */
    public function __construct(
        \Magento\Framework\Model\ResourceModel\Db\Context $context,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        $connectionName = null
    ) {
       parent::__construct($context,$connectionName);

       $this->storeManager = $storeManager;
    }

    protected function _construct()
    {
    }
}
