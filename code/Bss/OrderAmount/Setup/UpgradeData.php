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
 * @package    Bss_OrderAmount
 * @author     Extension Team
 * @copyright  Copyright (c) 2015-2019 BSS Commerce Co. ( http://bsscommerce.com )
 * @license    http://bsscommerce.com/Bss-Commerce-License.txt
 */

namespace Bss\OrderAmount\Setup;

use Magento\Framework\Setup\UpgradeDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;

/**
 * Class UpgradeData
 * @package Bss\OrderAmount\Setup
 */
class UpgradeData implements UpgradeDataInterface
{
    /**
     * @var \Magento\Framework\Serialize\SerializerInterface
     */
    protected $serializer;

    /**
     * UpgradeData constructor.
     * @param \Magento\Framework\Serialize\SerializerInterface $serializer
     */
    public function __construct(
        \Magento\Framework\Serialize\SerializerInterface $serializer
    ) {
        $this->serializer = $serializer;
    }

    /**
     * @param ModuleDataSetupInterface $setup
     * @param ModuleContextInterface $context
     */
    public function upgrade(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        $installer = $setup;
        $installer->startSetup();
        if (version_compare($context->getVersion(), '1.1.0', '<')) {
            $readAdapter = $installer->getConnection('core_read');
            $writeAdapter = $installer->getConnection('core_write');
            $tableName = $installer->getTable('core_config_data');
            //@codingStandardsIgnoreStart
            $select = $readAdapter->select()
                ->from(
                    [$tableName],
                    ['config_id', 'value']
                )
                ->where("path LIKE '%sales/minimum_order/amount%'");
            $old_config_data = $readAdapter->fetchAll($select);
            foreach ($old_config_data as $result) {
                if (preg_match('/^((s|i|d|b|a|O|C):|N;)/', $result['value'])) {
                    $convert = $this->serializer->serialize(unserialize($result['value']));
                    $writeAdapter->update($tableName, ['value' => $convert], ['config_id = ?' => $result['config_id']]);
                }
            }
            //@codingStandardsIgnoreEnd
        }
        $installer->endSetup();
    }
}
