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
 * @package    Bss_CatalogPermission
 * @author     Extension Team
 * @copyright  Copyright (c) 2018-2019 BSS Commerce Co. ( http://bsscommerce.com )
 * @license    http://bsscommerce.com/Bss-Commerce-License.txt
 */

namespace Bss\CatalogPermission\Setup;

use Magento\Framework\DB\Ddl\Table;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\Setup\UpgradeSchemaInterface;

class UpgradeSchema implements UpgradeSchemaInterface
{
    /**
     * @param SchemaSetupInterface $setup
     * @param ModuleContextInterface $context
     * @throws \Zend_Db_Exception
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function upgrade(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup->startSetup();
        if (version_compare($context->getVersion(), '1.1.0', '<=')) {
            $cmsTableName = $setup->getTable('cms_page');
            if ($setup->getConnection()->isTableExists($cmsTableName) == true) {
                $setup->getConnection()->addColumn(
                    $cmsTableName,
                    'bss_redirect_type',
                    [
                        'type' => Table::TYPE_TEXT,
                        'length' => '255',
                        'nullable' => false,
                        'comment' => 'Bss Redirect Type'
                    ]
                );
                $setup->getConnection()->addColumn(
                    $cmsTableName,
                    'bss_select_page',
                    [
                        'type' => Table::TYPE_TEXT,
                        'length' => '255',
                        'nullable' => false,
                        'comment' => 'Bss Select Page'
                    ]
                );
                $setup->getConnection()->addColumn(
                    $cmsTableName,
                    'bss_custom_url',
                    [
                        'type' => Table::TYPE_TEXT,
                        'length' => '255',
                        'nullable' => false,
                        'comment' => 'Bss Custom Url'
                    ]
                );
                $setup->getConnection()->addColumn(
                    $cmsTableName,
                    'bss_error_message',
                    [
                        'type' => Table::TYPE_TEXT,
                        'length' => '255',
                        'nullable' => false,
                        'comment' => 'Bss Error Message'
                    ]
                );
            }
        }
        $setup->endSetup();
    }
}
