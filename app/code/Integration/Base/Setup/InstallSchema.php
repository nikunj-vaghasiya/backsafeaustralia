<?php

namespace Integration\Base\Setup;

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

        $table = $installer->getConnection()->newTable(
            $installer->getTable('integration_pagination_log')
        )->addColumn(
            'api_entity_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
            'API Entity Id'
        )->addColumn(
            'log_type',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            11,
            ['nullable' => true],
            'Log Type'
        )->addColumn(
            'log_counter',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            11,
            ['nullable' => true],
            'Log Counter'
        )->addColumn(
            'current_auto_counter',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            11,
            ['nullable' => true],
            'Auto Sync Current Counter'
        )->addColumn(
            'last_auto_run',
            \Magento\Framework\DB\Ddl\Table::TYPE_DATETIME,
            null,
            ['nullable' => true],
            'Last Auto Run Date & Time'
        )->addColumn(
            'auto_status',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            11,
            ['nullable' => true],
            'Auto Sync Log Status (1 = New, 0 = Stop)'
        )->addColumn(
            'current_manual_counter',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            11,
            ['nullable' => true],
            'Manual Sync Current Counter'
        )->addColumn(
            'manual_status',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            11,
            ['nullable' => true],
            'Manual Sync Log Status (1 = New, 0 = Stop)'
        )->addColumn(
            'last_manual_run',
            \Magento\Framework\DB\Ddl\Table::TYPE_DATETIME,
            null,
            ['nullable' => true],
            'Last Manual Run Date & Time'
        )->addIndex(
            $installer->getIdxName(
                'integration_pagination_log',['api_entity_id'],
                \Magento\Framework\DB\Adapter\AdapterInterface::INDEX_TYPE_INDEX
            ),
            ['api_entity_id'],
            ['type' => \Magento\Framework\DB\Adapter\AdapterInterface::INDEX_TYPE_INDEX]
        )->setComment(
            'Integration Pagination Log'
        );

        $installer->getConnection()->createTable($table);
        $installer->endSetup();
    }
}