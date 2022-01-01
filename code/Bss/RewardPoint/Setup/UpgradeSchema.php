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

use Magento\Framework\DB\Ddl\Table;
use Magento\Framework\Setup\UpgradeSchemaInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\Setup\ModuleContextInterface;

/**
 * Class UpgradeSchema
 * @package Bss\RewardPoint\Setup
 */
class UpgradeSchema implements UpgradeSchemaInterface
{
    /**
     * @param SchemaSetupInterface $setup
     * @param ModuleContextInterface $context
     */
    public function upgrade(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $installer = $setup;

        $installer->startSetup();

        if (version_compare($context->getVersion(), '1.0.6', '<')) {
            $this->changeExpiredDateType($installer);
        }
        $installer->endSetup();
    }

    /**
     * @param SchemaSetupInterface $installer
     */
    private function changeExpiredDateType($installer)
    {
        $installer->getConnection()->addColumn(
            $installer->getTable('bss_reward_point_transaction'),
            'expires_at_back',
            [
                'type'     => Table::TYPE_DATETIME,
                'unsigned' => false,
                'nullable' => true,
                'comment'  => 'Expires At Back Up'
            ]
        );

        $rewardSelect = $installer->getConnection()->select()->from(
            $installer->getTable('bss_reward_point_transaction'),
            ['created_at', 'expires_at', 'transaction_id']
        );
        $dataReward = $installer->getConnection()->fetchAll($rewardSelect);
        $installer->getConnection()->changeColumn(
            $installer->getTable('bss_reward_point_transaction'),
            'expires_at',
            'expires_at',
            [
                'type'     => Table::TYPE_INTEGER,
                'unsigned' => false,
                'nullable' => false,
                'comment'  => 'Expires At'
            ]
        );

        foreach ($dataReward as $item) {
            $regEx = '/(\d{4})-(\d{2})-(\d{2}) (\d{2}):(\d{2}):(\d{2})/';
            $createdAt = $item['created_at'];
            $expiresAt = $item['expires_at'];

            if (preg_match($regEx, $expiresAt) && preg_match($regEx, $createdAt)) {
                $installer->getConnection()->update(
                    $installer->getTable('bss_reward_point_transaction'),
                    [
                        'expires_at' => round((strtotime($expiresAt) - strtotime($createdAt)) / 86400),
                        'expires_at_back' => $expiresAt
                    ],
                    [
                        'transaction_id = ?' => $item['transaction_id']
                    ]
                );
            }
        }
    }
}
