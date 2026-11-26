<?php
namespace Integration\Cron\Setup;

use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;

/**
 * @codeCoverageIgnore
 */
class InstallSchema implements InstallSchemaInterface
{
    /**
     * {@inheritdoc}
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    public function install(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
    	$installer = $setup;

        $installer->startSetup();

        /**
         * Create table 'integration_cron_buffer'
         */
        $table = $installer->getConnection()->newTable(
            $installer->getTable('integration_cron_buffer')
        )->addColumn(
            'api_entity_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
            'API Entity Id'
        )->addColumn(
            'schedule_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            11,
            ['nullable' => true],
            'Cron Schedule Table ID'
        )->addColumn(
            'website_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
            5,
            ['nullable' => true,'default' => null],
            'Website Id'
        )->addColumn(
            'mode',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            ['nullable' => true],
            'auto, manual'
        )->addColumn(
            'type',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            11,
            ['nullable' => true],
            '1 = Customer, 2 = Product'
        )->addColumn(
            'total_number',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            11,
            ['nullable' => true],
            'Batch Record'
        )->addColumn(
            'record_ids',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            null,
            ['nullable' => true],
            'No of records'
        )->addColumn(
            'status',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            ['nullable' => true],
            'Success, Pending'
        )->addColumn(
            'error_message',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            null,
            ['nullable' => true],
            'Error Message'
        )->addColumn(
            'start_at',
            \Magento\Framework\DB\Ddl\Table::TYPE_DATETIME,
            null,
            ['nullable' => false],
            'Start Time'
        )->addColumn(
            'end_at',
            \Magento\Framework\DB\Ddl\Table::TYPE_DATETIME,
            null,
            ['nullable' => false],
            'End Time'
        )->addColumn(
            'new_flag',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            10,
            ['nullable' => true],
            '0 = Imported, 1 = New Record'
        )->addColumn(
            'created_at',
            \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
            null,
            ['nullable' => false, 'default' => \Magento\Framework\DB\Ddl\Table::TIMESTAMP_INIT],
            'Created At (Current Server Time)'
        )->addColumn(
            'updated_at',
            \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
            null,
            ['nullable' => false, 'default' => \Magento\Framework\DB\Ddl\Table::TIMESTAMP_INIT_UPDATE],
            'Updated At (Current Server Time)'
        );

        $installer->getConnection()->createTable($table);
        $installer->endSetup();        
    }
}
