<?php

namespace Integration\Product\Setup;

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
		 * Creating table integration_customer
		 */
		$table = $installer->getConnection()->newTable(
			$installer->getTable('integration_product')
		)->addColumn(
			'api_entity_id',
			\Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
			9,
			['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
			'Entity Id'
		)->addColumn(
			'sku',
			\Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
			65,
			['nullable' => true,'default' => null],
			'SKU'
		)->addColumn(
			'status',
			\Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
			5,
			['nullable' => true,'default' => null],
			'Status, 1 = Enable, 2 = Disable'
		)->addColumn(
			'name',
			\Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
			255,
			['nullable' => true,'default' => null],
			'Product Name'
		)->addColumn(
			'price',
			\Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
			'12,4',
			['nullable' => true,'default' => null],
			'Price'
        )->addColumn(
			'special_price',
			\Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
			'12,4',
			['nullable' => true,'default' => null],
			'Special Price'
		)->addColumn(
			'quantity',
			\Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
			255,
			['nullable' => true],
			'QTY'
		)->addColumn(
			'qty_increments',
			\Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
			255,
			['nullable' => true,'default' => null],
			'QTY Increment'
		)->addColumn(
			'min_qty_allowed_in_cart',
			\Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
			255,
			['nullable' => true,'default' => null],
			'Minimum QTY allowed in cart'
		)->addColumn(
			'stock_status',
			\Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
			255,
			['nullable' => true,'default' => null],
			'Stock Status'
		)->addColumn(
			'height',
			\Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
			255,
			['nullable' => true,'default' => null],
			'Height'
		)->addColumn(
			'length',
			\Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
			255,
			['nullable' => true,'default' => null],
			'Length'
		)->addColumn(
			'width',
			\Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
			255,
			['nullable' => true,'default' => null],
			'Width'
		)->addColumn(
			'weight',
			\Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
			255,
			['nullable' => true,'default' => null],
			'Weight'
		)->addColumn(
			'net_weight',
			\Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
			255,
			['nullable' => true,'default' => null],
			'Net Weight'
		)->addColumn(
			'brand',
			\Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
			255,
			['nullable' => true,'default' => null],
			'Brand'
		)->addColumn(
			'website_id',
			\Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
			5,
			['nullable' => true,'default' => null],
			'Website Id'
		)->addColumn(
			'new_flag',
			\Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
			10,
			['nullable' => true,'default' => null],
			'1 => New, 0 => Updated'
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
