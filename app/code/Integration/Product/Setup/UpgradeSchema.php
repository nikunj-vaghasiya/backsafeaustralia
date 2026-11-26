<?php

namespace Integration\Product\Setup;

use Magento\Framework\Setup\UpgradeSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;

/**
 * @codeCoverageIgnore
 */
class UpgradeSchema implements UpgradeSchemaInterface
{
	public function upgrade(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
    	$installer = $setup;
        $installer->startSetup();
        if (version_compare($context->getVersion(), '1.0.1', '<')) {
        	$setup->getConnection()->addColumn(
	            $setup->getTable('integration_product'),
	            'supplier_code',
	            [
	                'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
	                'nullable' => true,
	                'length' => 255,
	                'comment' => 'Supplier Code'
	            ]
	        );
        	$setup->getConnection()->addColumn(
	            $setup->getTable('integration_product'),
	            'size',
	            [
	                'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
	                'nullable' => true,
	                'length' => 255,
	                'comment' => 'Size'
	            ]
	        );
	        $setup->getConnection()->addColumn(
	            $setup->getTable('integration_product'),
	            'color',
	            [
	                'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
	                'nullable' => true,
	                'length' => 255,
	                'comment' => 'Color'
	            ]
	        );
	        $setup->getConnection()->addColumn(
	            $setup->getTable('integration_product'),
	            'gender',
	            [
	                'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
	                'nullable' => true,
	                'length' => 255,
	                'comment' => 'Gender'
	            ]
	        );
	        $setup->getConnection()->addColumn(
	            $setup->getTable('integration_product'),
	            'glove_type',
	            [
	                'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
	                'nullable' => true,
	                'length' => 255,
	                'comment' => 'Glove Type'
	            ]
	        );
	        $setup->getConnection()->addColumn(
	            $setup->getTable('integration_product'),
	            'material',
	            [
	                'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
	                'nullable' => true,
	                'length' => 255,
	                'comment' => 'Material'
	            ]
	        );
	        $setup->getConnection()->addColumn(
	            $setup->getTable('integration_product'),
	            'warehouse',
	            [
	                'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
	                'nullable' => true,
	                'length' => 255,
	                'comment' => 'Warehouse'
	            ]
	        );
	        $setup->getConnection()->addColumn(
	            $setup->getTable('integration_product'),
	            'volume',
	            [
	                'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
	                'nullable' => true,
	                'length' => 255,
	                'comment' => 'Volume'
	            ]
	        );
        }
        if (version_compare($context->getVersion(), '1.0.2', '<')) {
        	$setup->getConnection()->addColumn(
	            $setup->getTable('integration_product'),
	            'conversation_factor',
	            [
	                'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
	                'nullable' => true,
	                'length' => 255,
	                'comment' => 'Conversation Factor'
	            ]
	        );
        	$setup->getConnection()->addColumn(
	            $setup->getTable('integration_product'),
	            'from_uom',
	            [
	                'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
	                'nullable' => true,
	                'length' => 255,
	                'comment' => 'From UOM'
	            ]
	        );
	        $setup->getConnection()->addColumn(
	            $setup->getTable('integration_product'),
	            'multiple_or_divide',
	            [
	                'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
	                'nullable' => true,
	                'length' => 255,
	                'comment' => 'Multiple Or Divide'
	            ]
	        );
	        $setup->getConnection()->addColumn(
	            $setup->getTable('integration_product'),
	            'to_uom',
	            [
	                'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
	                'nullable' => true,
	                'length' => 255,
	                'comment' => 'To UOM'
	            ]
	        );
        }
        if (version_compare($context->getVersion(), '1.0.3', '<')) {
        	$setup->getConnection()->addColumn(
	            $setup->getTable('integration_product'),
	            'base_uom',
	            [
	                'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
	                'nullable' => true,
	                'length' => 255,
	                'comment' => 'Base UOM'
	            ]
	        );
	        $setup->getConnection()->addColumn(
	            $setup->getTable('integration_product'),
	            'sales_uom',
	            [
	                'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
	                'nullable' => true,
	                'length' => 255,
	                'comment' => 'Sales UOM'
	            ]
	        );
	        $setup->getConnection()->addColumn(
	            $setup->getTable('integration_product'),
	            'purchase_uom',
	            [
	                'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
	                'nullable' => true,
	                'length' => 255,
	                'comment' => 'Purchase UOM'
	            ]
	        );
        }
        if (version_compare($context->getVersion(), '1.0.4', '<')) {
        	$setup->getConnection()->addColumn(
	            $setup->getTable('integration_product'),
	            'tax_category',
	            [
	                'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
	                'nullable' => true,
	                'length' => 255,
	                'comment' => 'Tax Category'
	            ]
	        );
        }
        if (version_compare($context->getVersion(), '1.0.5', '<')) {
        	$setup->getConnection()->addColumn(
	            $setup->getTable('integration_product'),
	            'supplier_inventory_id',
	            [
	                'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
	                'nullable' => true,
	                'length' => 255,
	                'comment' => 'Supplier Inventory Id'
	            ]
	        );
        }
        $installer->endSetup();
    }
}
