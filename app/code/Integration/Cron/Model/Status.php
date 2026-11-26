<?php
namespace Integration\Cron\Model;

class Status
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

    /**
     * Retrieve option array
     *
     * @return string[]
     */
    public static function getOptionArray()
    {
        return [
            self::CUSTOMER_DUMP_TYPE_ID             => "Customer Dump Process",
            self::CUSTOMER_IMPORT_BATCH_TYPE_ID     => "Customer Import Batch Process ",
            self::CUSTOMER_IMPORT_TYPE_ID           => "Customer Import Process",
            self::CUSTOMER_PARENT_ASSIGNMENT_IMPORT_BATCH_TYPE_ID => "Customer Parent Import Batch Process",
            self::CUSTOMER_PARENT_ASSIGNMENT_IMPORT_TYPE_ID => "Customer Parent Import Process",
            self::PRODUCT_DUMP_TYPE_ID              => "Product Dump Process",
            self::PRODUCT_IMPORT_BATCH_TYPE_ID      => "Product Import Batch Process",
            self::PRODUCT_IMPORT_TYPE_ID            => "Product Import Process",
            self::CUSTOMER_PRICING_GUEST_DUMP_TYPE_ID     => "Guest Customer Pricing Dump Process",
            self::CUSTOMER_PRICING_DUMP_TYPE_ID     => "Customer Pricing Dump Process",
            self::CUSTOMER_PRICING_BATCH_TYPE_ID    => "Customer Pricing Batch Process",
            self::CUSTOMER_PRICING_IMPORT_TYPE_ID   => "Customer Retail Pricing Import Process",
            self::CUSTOMER_DYNAMIC_PRICING_IMPORT_TYPE_ID   => "Customer Specific Pricing Import Process",
            self::CUSTOMER_DYNAMIC_PRICING_BATCH_TYPE_ID    => "Customer Specific Pricing Batch Process"
        ];
    }

    /**
     * Retrieve option array with empty value
     *
     * @return string[]
     */
    public function getAllOptions()
    {
        $result = [];

        foreach (self::getOptionArray() as $index => $value) {
            $result[] = ['value' => $index, 'label' => $value];
        }

        return $result;
    }

    /**
     * Retrieve option text by option value
     *
     * @param string $optionId
     * @return string
     */
    public function getOptionText($optionId)
    {
        $options = self::getOptionArray();

        return isset($options[$optionId]) ? $options[$optionId] : null;
    }
}
