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
 * @copyright  Copyright (c) 2017-2018 BSS Commerce Co. ( http://bsscommerce.com )
 * @license    http://bsscommerce.com/Bss-Commerce-License.txt
 */
namespace Bss\CatalogPermission\Setup;

use Magento\Catalog\Model\Category;
use Magento\Catalog\Model\ResourceModel\Eav\Attribute;
use Magento\Eav\Setup\EavSetupFactory;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\UpgradeDataInterface;

/**
 * Class UpgradeData
 *
 * @package Bss\CatalogPermission\Setup
 */
class UpgradeData implements UpgradeDataInterface
{
    /**
     * @var EavSetupFactory
     */
    private $eavSetupFactory;

    /**
     * UpgradeData constructor.
     * @param EavSetupFactory $eavSetupFactory
     */
    public function __construct(EavSetupFactory $eavSetupFactory)
    {
        $this->eavSetupFactory = $eavSetupFactory;
    }

    /**
     * Add eav category attribute
     *
     * @param ModuleDataSetupInterface $setup
     * @param ModuleContextInterface $context
     */
    public function upgrade(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup->startSetup();

        if (version_compare($context->getVersion(), '1.1.0', '<=')) {
            $eavSetup = $this->eavSetupFactory->create(['setup' => $setup]);
            $eavSetup->addAttribute(
                Category::ENTITY,
                'bss_redirect_type',
                [
                    'group' => 'catalog_permission',
                    'label' => 'Bss Redirect Type',
                    'type'  => 'int',
                    'input' => 'select',
                    'required' => false,
                    'sort_order' => 80,
                    'global' => Attribute::SCOPE_STORE,
                    'used_in_product_listing' => true,
                    'source' => \Bss\CatalogPermission\Model\Category\Attribute\Source\RedirectType::class
                ]
            );
            $eavSetup->addAttribute(
                Category::ENTITY,
                'bss_select_page',
                [
                    'group' => 'catalog_permission',
                    'label' => 'Bss Select Page',
                    'type'  => 'varchar',
                    'input' => 'select',
                    'required' => false,
                    'sort_order' => 85,
                    'global' => Attribute::SCOPE_STORE,
                    'used_in_product_listing' => true,
                    'source' => \Bss\CatalogPermission\Model\Config\Source\BssListCmsPage::class
                ]
            );
            $eavSetup->addAttribute(
                Category::ENTITY,
                'bss_custom_url',
                [
                    'group' => 'catalog_permission',
                    'label' => 'Bss Redirect Type',
                    'type'  => 'varchar',
                    'input' => 'text',
                    'required' => false,
                    'sort_order' => 90,
                    'global' => Attribute::SCOPE_STORE,
                    'used_in_product_listing' => true
                ]
            );
            $eavSetup->addAttribute(
                Category::ENTITY,
                'bss_error_message',
                [
                    'group' => 'catalog_permission',
                    'label' => 'Bss Error Message',
                    'type'  => 'varchar',
                    'input' => 'text',
                    'required' => false,
                    'sort_order' => 95,
                    'global' => Attribute::SCOPE_STORE,
                    'used_in_product_listing' => true
                ]
            );
        }
        $setup->endSetup();
    }
}
