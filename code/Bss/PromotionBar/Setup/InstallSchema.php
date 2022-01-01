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
 * @package    Bss_PromotionBar
 * @author     Extension Team
 * @copyright  Copyright (c) 2017-2018 BSS Commerce Co. ( http://bsscommerce.com )
 * @license    http://bsscommerce.com/Bss-Commerce-License.txt
 */
namespace Bss\PromotionBar\Setup;

use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;

class InstallSchema implements InstallSchemaInterface
{

    /**
     * Install table bss_promtionbar_bar
     *
     * @param SchemaSetupInterface $setup
     * @param ModuleContextInterface $context
     * @return void
     */
    public function install(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $installer = $setup;
        $installer->startSetup();
        if (!$installer->tableExists('bss_promotionbar_bar')) {
            $fileTable = $installer->getConnection()->newTable($installer->getTable('bss_promotionbar_bar'))
                ->addColumn(
                    'bar_id',
                    \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                    null,
                    ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
                    'ID'
                )
                ->addColumn(
                    'bar_name',
                    \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    null,
                    ['nullable' => false],
                    'Title'
                )
                ->addColumn(
                    'status',
                    \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
                    1,
                    ['nullable' => false],
                    'Status'
                )
                ->addColumn(
                    'bar_storeview',
                    \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    0,
                    ['nullable' => false],
                    'Store View'
                )
                ->addColumn(
                    'customer_group',
                    \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    0,
                    ['nullable' => false],
                    'Customer Group'
                )
                ->addColumn(
                    'display_from',
                    \Magento\Framework\DB\Ddl\Table::TYPE_DATETIME,
                    null,
                    ['nullable' => true],
                    'Display From'
                )->addColumn(
                    'display_to',
                    \Magento\Framework\DB\Ddl\Table::TYPE_DATETIME,
                    null,
                    ['nullable' => true],
                    'Display To'
                )->addColumn(
                    'priority',
                    \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                    0,
                    ['nullable' => false],
                    'Priority'
                )
                ->addColumn(
                    'page_display',
                    \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    0,
                    ['nullable' => false],
                    'Page Display'
                )
                ->addColumn(
                    'exclude_category',
                    \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    0,
                    ['nullable' => false],
                    'Exclude Categories'
                )
                ->addColumn(
                    'exclude_product',
                    \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    0,
                    ['nullable' => false],
                    'Exclude Product'
                )
                ->addColumn(
                    'position',
                    \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
                    0,
                    ['nullable' => false],
                    'Position'
                )
                ->addColumn(
                    'hide_after',
                    \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                    0,
                    ['nullable' => false],
                    'Hide After'
                )
                ->addColumn(
                    'bar_content',
                    \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    0,
                    ['nullable' => false],
                    'Exclude Product'
                )
                ->addColumn(
                    'bar_background',
                    \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    0,
                    ['nullable' => false],
                    'Background Color'
                )
                ->addColumn(
                    'bar_background',
                    \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    0,
                    ['nullable' => false],
                    'Background Color'
                )
                ->addColumn(
                    'bar_height',
                    \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                    0,
                    ['nullable' => false],
                    'Height'
                );

            $installer->getConnection()->createTable($fileTable);
            $installer->getConnection()->addIndex(
                $installer->getTable('bss_promotionbar_bar'),
                $setup->getIdxName(
                    $installer->getTable('bss_promotionbar_bar'),
                    ['bar_name'],
                    \Magento\Framework\DB\Adapter\AdapterInterface::INDEX_TYPE_FULLTEXT
                ),
                ['bar_name'],
                \Magento\Framework\DB\Adapter\AdapterInterface::INDEX_TYPE_FULLTEXT
            );
        }
        $installer->endSetup();
    }
}
