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
namespace Bss\RewardPoint\Model\Config\Source;

class TransactionActions implements \Magento\Framework\Option\ArrayInterface
{
    const ADMIN_CHANGE = 0;
    const REGISTRATION = 1;
    const BIRTHDAY = 2;
    const FIRST_REVIEW = 3;
    const REVIEW = 4;
    const FIRST_ORDER = 5;
    const ORDER = 6;
    const ORDER_REFUND = 7;
    const IMPORT = 8;
    const SUBSCRIBLE_NEWSLETTERS = 9;

    /**
     * @return array
     */
    public function toOptionArray()
    {
        return [
            ['value' => self::ADMIN_CHANGE, 'label' => __('Admin change')],
            ['value' => self::REGISTRATION, 'label' => __('Registration')],
            ['value' => self::BIRTHDAY, 'label' => __('Birthday')],
            ['value' => self::FIRST_REVIEW, 'label' => __('First Review')],
            ['value' => self::REVIEW, 'label' => __('Review')],
            ['value' => self::FIRST_ORDER, 'label' => __('First Order')],
            ['value' => self::ORDER, 'label' => __('Order')],
            ['value' => self::ORDER_REFUND, 'label' => __('Order Refund')],
            ['value' => self::IMPORT, 'label' => __('Import')],
            ['value' => self::SUBSCRIBLE_NEWSLETTERS, 'label' => __('Subscrible newsletters')],
        ];
    }

    /**
     * @return array
     */
    public function toArray()
    {
        return [
            self::ADMIN_CHANGE => __('Admin change'),
            self::REGISTRATION => __('Registration'),
            self::BIRTHDAY => __('Birthday'),
            self::FIRST_REVIEW => __('First Review'),
            self::REVIEW => __('Review'),
            self::FIRST_ORDER => __('First Order'),
            self::ORDER => __('Order'),
            self::ORDER_REFUND => __('Order Refund'),
            self::IMPORT => __('Import'),
            self::SUBSCRIBLE_NEWSLETTERS => __('Subscrible newsletters'),
        ];
    }
}
