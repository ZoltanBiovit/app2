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

class ReceivePoint extends \Magento\Eav\Model\Entity\Attribute\Source\AbstractSource
{
    const NO_POINT = 0;
    const EXCHANGE_RATE = 1;
    const FIX_AMOUNT = 2;

    protected $_options;

    /**
     * getAllOptions
     *
     * @return array
     */
    public function getAllOptions()
    {
        if ($this->_options === null) {
            $this->_options = [
                ['value' => self::NO_POINT, 'label' => __('No Point')],
                ['value' => self::EXCHANGE_RATE, 'label' => __('Exchange Rate')],
                ['value' => self::FIX_AMOUNT, 'label' => __('Fix amount')],
            ];
        }
        return $this->_options;
    }

    /**
     * @return array
     */
    public function toOptionArray()
    {
        return [
                ['value' => self::NO_POINT, 'label' => __('No Point')],
                ['value' => self::EXCHANGE_RATE, 'label' => __('Exchange rate')],
                ['value' => self::FIX_AMOUNT, 'label' => __('Fix amount')],
                ];
    }
}