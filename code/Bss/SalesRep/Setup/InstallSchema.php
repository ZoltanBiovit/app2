<?php
/**
 * BSS Commerce Co.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the EULA
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://bsscommerce.com/Bss-Commerce-License.txt
 *
 * @category   BSS
 * @package    Bss_SalesRep
 * @author     Extension Team
 * @copyright  Copyright (c) 2020 BSS Commerce Co. ( http://bsscommerce.com )
 * @license    http://bsscommerce.com/Bss-Commerce-License.txt
 */
namespace Bss\SalesRep\Setup;

use Magento\Framework\DB\Ddl\Table;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Zend_Db_Exception;

/**
 * Class InstallSchema
 *
 * @package Bss\SalesRep\Setup
 */
class InstallSchema implements \Magento\Framework\Setup\InstallSchemaInterface
{
    /**
     * Install table
     *
     * @param SchemaSetupInterface $setup
     * @param ModuleContextInterface $context
     * @throws Zend_Db_Exception
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function install(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $installer = $setup;
        $installer->startSetup();
        /**
         * Create table bss_sales_rep
         */
        $table = $installer->getConnection()->newTable(
            $installer->getTable('bss_sales_rep')
        )
            ->addColumn(
                'rep_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
                'Sales Rep ID'
            )
            ->addColumn(
                'user_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                10,
                ['unsigned' => true, 'nullable' => false],
                'User ID'
            )
            ->addColumn(
                'information',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                null,
                [],
                'Information'
            )
            ->addIndex(
                $installer->getIdxName('bss_sales_rep', ['rep_id']),
                ['rep_id']
            )
            ->addForeignKey(
                $setup->getFkName('admin_user', 'user_id', 'bss_sales_rep', 'user_id'),
                'user_id',
                $setup->getTable('admin_user'),
                'user_id',
                Table::ACTION_CASCADE
            )
            ->setComment(
                'Bss Sales Rep'
            );
        $installer->getConnection()->createTable($table);

        /**
         * Create table bss_sales_rep_order
         */
        $this->createTableSalesRepOrder($setup, $installer);

        /**
         * Create table bss_sales_rep_history
         */
        $this->createTableSalesRepHistory($setup, $installer);

        $installer->endSetup();
    }

    /**
     * Create table bss_sales_rep_order
     *
     * @param mixed $setup
     * @param mixed $installer
     */
    protected function createTableSalesRepOrder($setup, $installer)
    {
        $table = $installer->getConnection()->newTable(
            $installer->getTable('bss_sales_rep_order')
        )
            ->addColumn(
                'id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
                'Sales Rep Order ID'
            )
            ->addColumn(
                'order_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                10,
                ['unsigned' => true, 'nullable' => false],
                'Order ID'
            )
            ->addColumn(
                'user_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                10,
                ['unsigned' => true, 'nullable' => false],
                'User ID'
            )
            ->addIndex(
                $installer->getIdxName('bss_sales_rep_order', ['id']),
                ['id']
            )
            ->addForeignKey(
                $setup->getFkName('admin_user', 'user_id', 'bss_sales_rep_order', 'user_id'),
                'user_id',
                $setup->getTable('admin_user'),
                'user_id',
                Table::ACTION_CASCADE
            )
            ->addForeignKey(
                $setup->getFkName('sales_order', 'entity_id', 'bss_sales_rep_order', 'order_id'),
                'order_id',
                $setup->getTable('sales_order'),
                'entity_id',
                Table::ACTION_CASCADE
            )
            ->setComment(
                'Bss Sales Rep Order'
            );
        $installer->getConnection()->createTable($table);
    }

    /**
     * Create table bss_sales_rep_history
     *
     * @param mixed $setup
     * @param mixed $installer
     */
    protected function createTableSalesRepHistory($setup, $installer)
    {
        $table = $installer->getConnection()->newTable(
            $installer->getTable('bss_sales_rep_history')
        )
            ->addColumn(
                'id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
                'Sales Rep History ID'
            )
            ->addColumn(
                'user_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                10,
                ['unsigned' => true, 'nullable' => false],
                'User ID'
            )
            ->addColumn(
                'customer_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                10,
                ['unsigned' => true, 'nullable' => false],
                'Customer ID'
            )
            ->addColumn(
                'history',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                255,
                ['unsigned' => true, 'nullable' => false],
                'History'
            )
            ->addColumn(
                'update_at',
                \Magento\Framework\DB\Ddl\Table::TYPE_DATETIME,
                null,
                ['unsigned' => true, 'nullable' => false],
                'Update At'
            )
            ->addColumn(
                'type',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                10,
                ['unsigned' => true, 'nullable' => false],
                'Type'
            )
            ->addIndex(
                $installer->getIdxName('bss_sales_rep_history', ['id']),
                ['id']
            )
            ->addForeignKey(
                $setup->getFkName('admin_user', 'user_id', 'bss_sales_rep_history', 'user_id'),
                'user_id',
                $setup->getTable('admin_user'),
                'user_id',
                Table::ACTION_CASCADE
            )
            ->setComment(
                'Bss Sales Rep History'
            );
        $installer->getConnection()->createTable($table);
    }
}
