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

use Bss\SalesRep\Helper\OrderCreatedByAdmin;
use Magento\Framework\DB\Ddl\Table;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\Setup\UpgradeSchemaInterface;
use Zend_Db_Exception;

/**
 * Class UpgradeSchema
 *
 * @package Bss\SalesRep\Setup
 */
class UpgradeSchema implements UpgradeSchemaInterface
{
    /**
     * @var OrderCreatedByAdmin
     */
    protected $orderCreatedByAdmin;

    /**
     * UpgradeSchema constructor.
     *
     * @param OrderCreatedByAdmin $orderCreatedByAdmin
     */
    public function __construct(
        OrderCreatedByAdmin $orderCreatedByAdmin
    ) {
        $this->orderCreatedByAdmin = $orderCreatedByAdmin;
    }

    /**
     * Upgrade module
     *
     * @param SchemaSetupInterface $setup
     * @param ModuleContextInterface $context
     * @throws Zend_Db_Exception
     */
    public function upgrade(
        SchemaSetupInterface $setup,
        ModuleContextInterface $context
    ) {
        $installer = $setup;
        $installer->startSetup();
        if ($context->getVersion() == '') {
            $table = $installer->getTable('authorization_role');
            $connection = $installer->getConnection();
            $connection->addColumn(
                $table,
                'is_sales_rep',
                [
                    'type' => Table::TYPE_INTEGER,
                    'nullable' => true,
                    'default' => null,
                    'comment' => 'Is Sales Rep'
                ]
            );
        }
        if (version_compare($context->getVersion(), '1.0.2', '<=')) {
            $this->createTableOrderCreatedByAdmin($installer);
            $this->addColumnUserId($installer);
        }
        $installer->endSetup();
    }

    /**
     * Create table order_created_by_admin
     *
     * @param SchemaSetupInterface $installer
     * @throws Zend_Db_Exception
     */
    public function createTableOrderCreatedByAdmin($installer)
    {
        if (!$installer->tableExists('order_created_by_admin')) {
            $table = $installer->getConnection()->newTable(
                $installer->getTable('order_created_by_admin')
            )
                ->addColumn(
                    'id',
                    Table::TYPE_SMALLINT,
                    null,
                    [
                        'identity' => true,
                        'nullable' => false,
                        'primary' => true,
                        'unsigned' => true,
                    ],
                    'ID'
                )->addColumn(
                    'order_id',
                    Table::TYPE_SMALLINT,
                    null,
                    [
                        'nullable => true',
                    ],
                    'Order Id'
                )
                ->addColumn(
                    'created_by_admin',
                    Table::TYPE_BOOLEAN,
                    null,
                    ['nullable => true'],
                    'Create By Admin'
                )
                ->addIndex(
                    $installer->getIdxName('order_created_by_admin', ['id']),
                    ['id']
                )
                ->addIndex(
                    $installer->getIdxName('order_id', ['order_id']),
                    ['order_id'],
                    ['type' => \Magento\Framework\DB\Adapter\AdapterInterface::INDEX_TYPE_UNIQUE]
                )
                ->setComment('Order Created By Admin');
            $installer->getConnection()->createTable($table);
            $this->orderCreatedByAdmin->saveOrderAdminFromSalesOrder();
        } else {
            $this->orderCreatedByAdmin->saveOrderAdminFromSalesOrder("update");
        }
    }

    /**
     * Add column user_id into table quote_extension
     *
     * @param SchemaSetupInterface $installer
     */
    public function addColumnUserId(SchemaSetupInterface $installer)
    {
        $tableName = $installer->getTable('quote_extension');
        if ($installer->getConnection()->isTableExists($tableName) == true) {
            if (!$installer->getConnection()->tableColumnExists($installer->getTable($tableName), "user_id")) {
                $connection = $installer->getConnection();
                $connection->addColumn(
                    $tableName,
                    'user_id',
                    [
                        'type' => \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                        'nullable' => true,
                        'default' => null,
                        'comment' => 'User Id'
                    ]
                );
            }
        }
    }
}
