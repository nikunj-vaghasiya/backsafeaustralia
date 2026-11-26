<?php

namespace Integration\ManualSync\Helper;

class Data extends \Integration\Base\Helper\AbstractHelper
{
    const CUSTOMER_DUMP_TYPE_ID = 1;
    const CUSTOMER_IMPORT_BATCH_TYPE_ID = 2;
    const CUSTOMER_IMPORT_TYPE_ID = 3;
    const PRODUCT_DUMP_TYPE_ID = 4;
    const PRODUCT_IMPORT_BATCH_TYPE_ID = 5;
    const PRODUCT_IMPORT_TYPE_ID = 6;
    const CUSTOMER_PRICING_DUMP_TYPE_ID = 13;
    const CUSTOMER_PRICING_GUEST_DUMP_TYPE_ID = 14;
    const CUSTOMER_PRICING_BATCH_TYPE_ID = 15;
    const CUSTOMER_PRICING_IMPORT_TYPE_ID = 17;
    const CUSTOMER_DYNAMIC_PRICING_IMPORT_TYPE_ID = 22;
    const CUSTOMER_DYNAMIC_PRICING_BATCH_TYPE_ID = 24;
    const CUSTOMER_PARENT_ASSIGNMENT_IMPORT_BATCH_TYPE_ID = 26;
    const CUSTOMER_PARENT_ASSIGNMENT_IMPORT_TYPE_ID = 27;
    /* There is no actual dump process shedule for this type. It is use to separate the process id for parent import */
    const CUSTOMER_PARENT_ASSIGNMENT_DUMP_TYPE_ID = 28;

    /* NZ WEBSITE */
    const NZ_CUSTOMER_DUMP_TYPE_ID = 7;
    const NZ_CUSTOMER_IMPORT_BATCH_TYPE_ID = 8;
    const NZ_CUSTOMER_IMPORT_TYPE_ID = 9;
    const NZ_PRODUCT_DUMP_TYPE_ID = 10;
    const NZ_PRODUCT_IMPORT_BATCH_TYPE_ID = 11;
    const NZ_PRODUCT_IMPORT_TYPE_ID = 12;
    const NZ_CUSTOMER_PRICING_DUMP_TYPE_ID = 18;
    const NZ_CUSTOMER_PRICING_GUEST_DUMP_TYPE_ID = 19;
    const NZ_CUSTOMER_PRICING_BATCH_TYPE_ID = 20;
    const NZ_CUSTOMER_PRICING_IMPORT_TYPE_ID = 21;
    const NZ_CUSTOMER_DYNAMIC_PRICING_IMPORT_TYPE_ID = 23;
    const NZ_CUSTOMER_DYNAMIC_PRICING_BATCH_TYPE_ID = 25;
    /* NZ WEBSITE */

    const BATCH_TIMEOUT_MINUTES = 3;

    const AUTO_MODE = 'auto';
    const MANUAL_MODE = 'manual';

    const NZ_WEBSITE_STORE_ID = 2;
    const AU_WEBSITE_STORE_ID = 1;

    const AU_WEBSITE_ID = 1;
    const NZ_WEBSITE_ID = 2;
    /**
     * Store manager
     *
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * Currently selected store ID if applicable
     *
     * @var int
     */
    protected $_storeId;

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $scopeConfig;


    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\Filesystem\DirectoryList $dir,
        \Psr\Log\LoggerInterface $logger
    ) {
        parent::__construct($context, $storeManager, $dir, $logger);
    }

    public function getEntityDirNameByType($type)
    {
        $dirName = '';

        if (!$type) {
            return '';
        }

        switch ($type) {
            case self::CUSTOMER_DUMP_TYPE_ID:
                $dirName = 'customer/dump';
                break;
            case self::CUSTOMER_IMPORT_BATCH_TYPE_ID:
                $dirName = 'customer/batch';
                break;
            case self::CUSTOMER_IMPORT_TYPE_ID:
                $dirName = 'customer/import';
                break;
            case self::CUSTOMER_PARENT_ASSIGNMENT_IMPORT_BATCH_TYPE_ID:
                $dirName = 'customer/batch/parent';
                break;
            case self::CUSTOMER_PARENT_ASSIGNMENT_IMPORT_TYPE_ID:
                $dirName = 'customer/import/parent';
                break;
            case self::PRODUCT_DUMP_TYPE_ID:
                $dirName = 'product/dump';
                break;
            case self::PRODUCT_IMPORT_BATCH_TYPE_ID:
                $dirName = 'product/batch';
                break;
            case self::PRODUCT_IMPORT_TYPE_ID:
                $dirName = 'product/import';
                break;
            case self::CUSTOMER_PRICING_GUEST_DUMP_TYPE_ID:
                $dirName = 'pricing/dump';
                break;
            case self::CUSTOMER_PRICING_BATCH_TYPE_ID:
                $dirName = 'pricing/batch';
                break;
            case self::CUSTOMER_PRICING_IMPORT_TYPE_ID:
                $dirName = 'pricing/import';
                break;
            case self::CUSTOMER_PRICING_DUMP_TYPE_ID:
                $dirName = 'pricing/dump/customer';
                break;
            case self::CUSTOMER_DYNAMIC_PRICING_BATCH_TYPE_ID:
                $dirName = 'pricing/batch/customer';
                break;
            case self::CUSTOMER_DYNAMIC_PRICING_IMPORT_TYPE_ID:
                $dirName = 'pricing/import/customer';
                break;
            default:
                $dirName = '';
                break;
        }

        return $dirName;
    }

    public function getLogDescriptionByFileName($filename)
    {
        /*var_dump($filename);die;*/
        $description = [
            /* customer */
            'customer/dump/success.log' => 'File store logs during dump new customer or update existing customer from Integration system to magento custom table',
            'customer/dump/error.log' => 'File store error logs during create new customer or update the existing customer from Integration system to magento custom table',
            'customer/import/success.log' => 'File store logs during import new customer or update existing customer from custom table to magento',
            'customer/import/error.log' => 'File store error logs during create new customer or update the existing customer from custom table to magento',
            /* customer */

            /* product */
            'product/dump/success.log' => 'It logs the information of dump the new product or update of existing product from Integration system to magento custom table.',
            'product/dump/error.log' => 'It logs the errors encountered during create new or update product from Integration system to magento custom table.',
            'product/batch/success.log' => 'File store logs all added/updated products records from Integration system and schedule batch to import the data.',
            'product/batch/error.log' => 'File store error logs during added/updated products records from Integration system and schedule batch to import the data',
            'product/import/success.log' => 'File store logs during import new product or update existing product from custom table to magento',
            'product/import/error.log' => 'File store error logs during create new product or update the existing product from custom table to magento'
            /* product */

        ];

        return (isset($description[$filename]) && $description[$filename] != '') ? $description[$filename] : 'Log description not available.';
    }

    public function getCurrentDatetime($format = 'Y-m-d H:i:s')
    {
        $date = date($format, time());
        return $date;
    }

    public function getTitleTextByTypeId($typeId)
    {
        $titles = [
            self::CUSTOMER_DUMP_TYPE_ID             => "Customer Dump Process",
            self::CUSTOMER_IMPORT_BATCH_TYPE_ID     => "Customer Import Batch Process",
            self::CUSTOMER_IMPORT_TYPE_ID           => "Customer Import Process",
            self::CUSTOMER_PARENT_ASSIGNMENT_IMPORT_BATCH_TYPE_ID => "Customer Parent Import Batch Process",
            self::CUSTOMER_PARENT_ASSIGNMENT_IMPORT_TYPE_ID => "Customer Parent Import Process",
            self::PRODUCT_DUMP_TYPE_ID              => "Product Dump Process",
            self::PRODUCT_IMPORT_BATCH_TYPE_ID      => "Product Import Batch Process",
            self::PRODUCT_IMPORT_TYPE_ID            => "Product Import Process",
            self::CUSTOMER_PRICING_GUEST_DUMP_TYPE_ID     => "Guest/Retail Customer Pricing Dump Process",
            self::CUSTOMER_PRICING_BATCH_TYPE_ID    => "Guest/Retail Customer Pricing Batch Process",
            self::CUSTOMER_PRICING_IMPORT_TYPE_ID   => "Guest/Retail Customer Import Process",
            self::CUSTOMER_PRICING_DUMP_TYPE_ID     => "Customer Pricing Dump Process",
            self::CUSTOMER_DYNAMIC_PRICING_BATCH_TYPE_ID     => "Customer Specific Pricing Batch Process",
            self::CUSTOMER_DYNAMIC_PRICING_IMPORT_TYPE_ID     => "Customer Specific Pricing Import Process",
        ];

        return (isset($titles[$typeId])) ? $titles[$typeId] : '';
    }

    public function getMelbourneCurrentDateTime($format = 'Y-m-d H:i:s')
    {
        $currentTimezone = date_default_timezone_get();
        date_default_timezone_set('Australia/Melbourne');
        $date = date($format, time());
        date_default_timezone_set($currentTimezone);
        return $date;
    }

    public function getCurrentDate($format = 'Y-m-d')
    {
        $currentTimezone = date_default_timezone_get();
        date_default_timezone_set('Australia/Melbourne');
        $date = date($format, time());
        date_default_timezone_set($currentTimezone);
        return $date;
    }

    public function getPreviousDate($format = 'Y-m-d')
    {
        $currentTimezone = date_default_timezone_get();
        date_default_timezone_set('Australia/Melbourne');
        $date = date($format, strtotime('-20 days'));
        date_default_timezone_set($currentTimezone);
        return $date;
    }

    public function getWebsiteIdByStoreId(int $storeId)
    {
        $websiteId = $this->_storeManager->getStore($storeId)->getWebsiteId();
        return $websiteId;
    }
}
