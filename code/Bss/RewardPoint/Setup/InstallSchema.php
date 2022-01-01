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
 * @package    Bss_RewardPoint
 * @author     Extension Team
 * @copyright  Copyright (c) 2019-2020 BSS Commerce Co. ( http://bsscommerce.com )
 * @license    http://bsscommerce.com/Bss-Commerce-License.txt
 */
namespace Bss\RewardPoint\Setup;

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
         * Create table 'bss_reward_point_notification'
         */
        $table = $installer->getConnection()->newTable(
            $installer->getTable('bss_reward_point_notification')
        )->addColumn(
            'notification_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['unsigned' => false, 'nullable' => false, 'identity' => true, 'primary' => true],
            'Notification Id'
        )->addColumn(
            'customer_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['unsigned' => true],
            'Customer ID'
        )->addColumn(
            'notify_balance',
            \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
            null,
            ['unsigned' => true, 'nullable' => false, 'default' => '0'],
            'Email notify for balance updated'
        )->addColumn(
            'notify_expiration',
            \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
            null,
            ['unsigned' => true, 'nullable' => false, 'default' => '0'],
            'Email notify for expiration points'
        )->addForeignKey(
            $installer->getFkName('bss_reward_point_notification', 'customer_id', 'customer_entity', 'entity_id'),
            'customer_id',
            $installer->getTable('customer_entity'),
            'entity_id',
            \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
        )->addIndex(
            $installer->getIdxName('bss_reward_point_notification', ['notification_id']),
            ['notification_id']
        )->setComment(
            'Email Notifocation'
        );
        $installer->getConnection()->createTable($table);

        /**
         * Create table 'bss_reward_point_rule'
         */
        $table = $installer->getConnection()->newTable(
            $installer->getTable('bss_reward_point_rule')
        )->addColumn(
            'rule_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
            'Rule Id'
        )->addColumn(
            'name',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            ['nullable' => true],
            'Name'
        )->addColumn(
            'type',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            32,
            [],
            'Rule type'
        )->addColumn(
            'from_date',
            \Magento\Framework\DB\Ddl\Table::TYPE_DATE,
            null,
            ['nullable' => true, 'default' => null],
            'From'
        )->addColumn(
            'to_date',
            \Magento\Framework\DB\Ddl\Table::TYPE_DATE,
            null,
            ['nullable' => true, 'default' => null],
            'To'
        )->addColumn(
            'is_active',
            \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
            null,
            ['nullable' => false, 'default' => '0'],
            'Is Active'
        )->addColumn(
            'conditions_serialized',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            '2M',
            [],
            'Conditions Serialized'
        )->addColumn(
            'actions_serialized',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            '2M',
            [],
            'Actions Serialized'
        )->addColumn(
            'product_ids',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            '64k',
            [],
            'Product Ids'
        )->addColumn(
            'priority',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['unsigned' => true, 'nullable' => false, 'default' => '0'],
            'Sort Order'
        )->addColumn(
            'simple_action',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            32,
            [],
            'Simple Action'
        )->addColumn(
            'point',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            [],
            'Fixed Point'
        )->addColumn(
            'purchase_point',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            [],
            'Purchase Point'
        )->addColumn(
            'spent_amount',
            \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
            [12, 4],
            ['nullable' => false, 'default' => '0.0000'],
            'Spent Amount'
        )->addIndex(
            $installer->getIdxName('bss_reward_point_rule', ['is_active', 'to_date', 'from_date']),
            ['is_active', 'to_date', 'from_date']
        )->setComment(
            'Reward Point Rule'
        );

        $installer->getConnection()->createTable($table);

        /**
         * Create table 'bss_reward_point_rule_website'
         */
        $table = $installer->getConnection()->newTable(
            $installer->getTable('bss_reward_point_rule_website')
        )->addColumn(
            'rule_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['unsigned' => true, 'nullable' => false, 'primary' => true],
            'Rule Id'
        )->addColumn(
            'website_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
            null,
            ['unsigned' => true, 'nullable' => false, 'primary' => true],
            'Website Id'
        )->addIndex(
            $installer->getIdxName('bss_reward_point_rule_website', ['website_id']),
            ['website_id']
        )->addForeignKey(
            $installer->getFkName('bss_reward_point_rule_website', 'rule_id', 'bss_reward_point_rule', 'rule_id'),
            'rule_id',
            $installer->getTable('bss_reward_point_rule'),
            'rule_id',
            \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
        )->addForeignKey(
            $installer->getFkName('bss_reward_point_rule_website', 'website_id', 'store_website', 'website_id'),
            'website_id',
            $installer->getTable('store_website'),
            'website_id',
            \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
        )->setComment(
            'Reward Point To Websites Relations'
        );

        $installer->getConnection()->createTable($table);

        /**
         * Create table 'bss_reward_point_rule_customer_group'
         */
        $table = $installer->getConnection()->newTable(
            $installer->getTable('bss_reward_point_rule_customer_group')
        )->addColumn(
            'rule_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['unsigned' => true, 'nullable' => false, 'primary' => true],
            'Rule Id'
        )->addColumn(
            'customer_group_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['unsigned' => true, 'nullable' => false, 'primary' => true],
            'Customer Group Id'
        )->addIndex(
            $installer->getIdxName('bss_reward_point_rule_customer_group', ['customer_group_id']),
            ['customer_group_id']
        )->addForeignKey(
            $installer->getFkName(
                'bss_reward_point_rule_customer_group',
                'rule_id',
                'bss_reward_point_rule',
                'rule_id'
            ),
            'rule_id',
            $installer->getTable('bss_reward_point_rule'),
            'rule_id',
            \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
        )->addForeignKey(
            $installer->getFkName(
                'bss_reward_point_rule_customer_group',
                'customer_group_id',
                'customer_group',
                'customer_group_id'
            ),
            'customer_group_id',
            $installer->getTable('customer_group'),
            'customer_group_id',
            \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
        )->setComment(
            'Reward Point To Customer Groups Relations'
        );

        $installer->getConnection()->createTable($table);
        /**
         * Create table 'bss_reward_point_rule_note'
         */
        $table = $installer->getConnection()->newTable(
            $installer->getTable('bss_reward_point_rule_note')
        )->addColumn(
            'note_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
            'Note Id'
        )->addColumn(
            'rule_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['unsigned' => true, 'nullable' => false],
            'Rule Id'
        )->addColumn(
            'store_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
            null,
            ['unsigned' => true, 'nullable' => false],
            'Store Id'
        )->addColumn(
            'note',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            '2M',
            [],
            'Note'
        )->addIndex(
            $installer->getIdxName(
                'bss_reward_point_rule_note',
                ['rule_id', 'store_id'],
                \Magento\Framework\DB\Adapter\AdapterInterface::INDEX_TYPE_UNIQUE
            ),
            ['rule_id', 'store_id'],
            ['type' => \Magento\Framework\DB\Adapter\AdapterInterface::INDEX_TYPE_UNIQUE]
        )->addIndex(
            $installer->getIdxName('bss_reward_point_rule_note', ['store_id']),
            ['store_id']
        )->addForeignKey(
            $installer->getFkName('bss_reward_point_rule_note', 'rule_id', 'bss_reward_point_rule', 'rule_id'),
            'rule_id',
            $installer->getTable('bss_reward_point_rule'),
            'rule_id',
            \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
        )->addForeignKey(
            $installer->getFkName('bss_reward_point_rule_note', 'store_id', 'store', 'store_id'),
            'store_id',
            $installer->getTable('store'),
            'store_id',
            \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
        )->setComment(
            'Reward Point Label'
        );

        $installer->getConnection()->createTable($table);

        /**
         * Create table 'bss_reward_point_rate'
         */
        $table = $installer->getConnection()->newTable(
            $installer->getTable('bss_reward_point_rate')
        )->addColumn(
            'rate_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
            'Rate Id'
        )->addColumn(
            'status',
            \Magento\Framework\DB\Ddl\Table::TYPE_BOOLEAN,
            null,
            ['nullable' => false, 'default' => 0],
            'Status'
        )->addColumn(
            'website_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
            null,
            ['unsigned' => true, 'nullable' => false],
            'Website ID'
        )->addColumn(
            'customer_group_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
            null,
            ['unsigned' => true, 'nullable' => false],
            'Customer Group Id'
        )->addColumn(
            'base_currrency_code',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            ['nullable' => true],
            'Base Currency Code'
        )->addColumn(
            'basecurrency_to_point_rate',
            \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
            '24,12',
            [],
            'Base Currency To Point Rate'
        )->setComment(
            'Reward Point Rate'
        );

        $installer->getConnection()->createTable($table);

        /**
         * Create table 'bss_reward_point_transaction'
         */
        $table = $installer->getConnection()->newTable(
            $installer->getTable('bss_reward_point_transaction')
        )->addColumn(
            'transaction_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
            'Transaction Id'
        )->addColumn(
            'website_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
            null,
            ['unsigned' => true, 'nullable' => false],
            'Website ID'
        )->addColumn(
            'customer_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['unsigned' => true],
            'Customer ID'
        )->addColumn(
            'point',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['unsigned' => false, 'nullable' => true, 'default' => '0'],
            'Point'
        )->addColumn(
            'point_used',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['unsigned' => false, 'nullable' => true, 'default' => '0'],
            'Point Used'
        )->addColumn(
            'point_expired',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['unsigned' => false, 'nullable' => true, 'default' => '0'],
            'Point Expired'
        )->addColumn(
            'amount',
            \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
            [12, 4],
            ['nullable' => false, 'default' => '0.0000'],
            'Amount'
        )->addColumn(
            'base_currrency_code',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            ['nullable' => true],
            'Base Currency Code'
        )->addColumn(
            'basecurrency_to_point_rate',
            \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
            [24, 12],
            [],
            'Base Currency To Point Rate'
        )->addColumn(
            'action_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['unsigned' => true],
            'Action rewards id'
        )->addColumn(
            'action',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            ['nullable' => true],
            'Action rewards'
        )->addColumn(
            'created_at',
            \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
            null,
            ['nullable' => false, 'default' => \Magento\Framework\DB\Ddl\Table::TIMESTAMP_INIT],
            'Created At'
        )->addColumn(
            'note',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            '2M',
            [],
            'Note'
        )->addColumn(
            'created_by',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            ['nullable' => true],
            'Created by'
        )->addColumn(
            'is_expired',
            \Magento\Framework\DB\Ddl\Table::TYPE_BOOLEAN,
            null,
            ['nullable' => false, 'default' => 0],
            'Is Expired'
        )->addColumn(
            'expires_at',
            \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
            null,
            ['unsigned' => false, 'nullable' => true],
            'Expires At'
        )->addForeignKey(
            $installer->getFkName('bss_reward_point_transaction', 'customer_id', 'customer_entity', 'entity_id'),
            'customer_id',
            $installer->getTable('customer_entity'),
            'entity_id',
            \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
        )->addIndex(
            $installer->getIdxName('bss_reward_point_transaction', ['transaction_id']),
            ['transaction_id']
        )->setComment(
            'Reward Point Transactions'
        );
        $installer->getConnection()->createTable($table);
        // erarning point
        $installer->getConnection()->addColumn(
            $installer->getTable('quote'),
            'earn_points',
            [
                'type' => \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                'unsigned' => false,
                'nullable' => true,
                'comment' => 'Earn reward point'
            ]
        );

        $installer->getConnection()->addColumn(
            $installer->getTable('quote_address'),
            'earn_points',
            [
                'type' => \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                'unsigned' => false,
                'nullable' => true,
                'comment' => 'Earn reward point'
            ]
        );

        $installer->getConnection()->addColumn(
            $installer->getTable('quote'),
            'rwp_note',
            [
                'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                'nullable' => false,
                'LENGTH' => '2M',
                'comment' => 'Note'
            ]
        );
        // spend point
        $installer->getConnection()->addColumn(
            $installer->getTable('quote'),
            'spend_points',
            [
                'type' => \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                'unsigned' => false,
                'nullable' => true,
                'comment' => 'Use Reward Points'
            ]
        );

        $installer->getConnection()->addColumn(
            $installer->getTable('quote'),
            'rwp_amount',
            [
                'type' => \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                'length' => '12,4',
                'comment' => 'Reward Points Amount'
            ]
        );

        $installer->getConnection()->addColumn(
            $installer->getTable('quote'),
            'base_rwp_amount',
            [
                'type' => \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                'length' => '12,4',
                'comment' => 'Base Reward Points Amount'
            ]
        );


        $installer->getConnection()->addColumn(
            $installer->getTable('quote_address'),
            'rwp_amount',
            [
                'type' => \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                'length' => '12,4',
                'comment' => 'Reward Points Amount'
            ]
        );

        $installer->getConnection()->addColumn(
            $installer->getTable('quote_address'),
            'base_rwp_amount',
            [
                'type' => \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                'length' => '12,4',
                'comment' => 'Base Reward Points Amount'
            ]
        );

        $installer->getConnection()->addColumn(
            $installer->getTable('sales_order'),
            'rwp_amount',
            [
                'type' => \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                'length' => '12,4',
                'comment' => 'Reward Points Amount'
            ]
        );

        $installer->getConnection()->addColumn(
            $installer->getTable('sales_order'),
            'base_rwp_amount',
            [
                'type' => \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                'length' => '12,4',
                'comment' => 'Base Reward Points Amount'
            ]
        );

        $installer->getConnection()->addColumn(
            $installer->getTable('sales_order'),
            'rwp_amount_invoiced',
            [
                'type' => \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                'length' => '12,4',
                'comment' => 'Reward Points Amount Invoiced'
            ]
        );

        $installer->getConnection()->addColumn(
            $installer->getTable('sales_order'),
            'base_rwp_amount_invoiced',
            [
                'type' => \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                'length' => '12,4',
                'comment' => 'Base Reward Points Amount Invoiced'
            ]
        );

        $installer->getConnection()->addColumn(
            $installer->getTable('sales_order'),
            'rwp_amount_refunded',
            [
                'type' => \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                'length' => '12,4',
                'comment' => 'Reward Points Amount Refunded'
            ]
        );

        $installer->getConnection()->addColumn(
            $installer->getTable('sales_order'),
            'base_rwp_amount_refunded',
            [
                'type' => \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                'length' => '12,4',
                'comment' => 'Base Reward Points Refunded'
            ]
        );

        $installer->getConnection()->addColumn(
            $installer->getTable('sales_invoice'),
            'rwp_amount',
            [
                'type' => \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                'length' => '12,4',
                'comment' => 'Reward Points Amount'
            ]
        );

        $installer->getConnection()->addColumn(
            $installer->getTable('sales_invoice'),
            'base_rwp_amount',
            [
                'type' => \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                'length' => '12,4',
                'comment' => 'Base Reward Points Amount'
            ]
        );

        $installer->getConnection()->addColumn(
            $installer->getTable('sales_creditmemo'),
            'rwp_amount',
            [
                'type' => \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                'length' => '12,4',
                'comment' => 'Reward Points Amount'
            ]
        );

        $installer->getConnection()->addColumn(
            $installer->getTable('sales_creditmemo'),
            'base_rwp_amount',
            [
                'type' => \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                'length' => '12,4',
                'comment' => 'Base Reward Points Amount'
            ]
        );

        $installer->endSetup();
    }
}
