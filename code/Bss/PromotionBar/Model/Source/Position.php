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
namespace Bss\PromotionBar\Model\Source;

/**
 * Class Position
 * @package Bss\PromotionBar\Model\Source
 */
class Position implements \Magento\Framework\Data\OptionSourceInterface
{
    /**
     * Position values
     */
    const PAGE_TOP = 1;
    const MENU_TOP = 2;
    const MENU_BOTTOM = 3;
    const CONTENT_TOP = 4;
    const PAGE_BOTTOM = 5;

    /**
     * To Option Array
     *
     * @return array
     */
    public function toOptionArray()
    {
        return [
            ['value' => self::PAGE_TOP,  'label' => __('Top of Page')],
            ['value' => self::MENU_TOP,  'label' => __('Above Menu')],
            ['value' => self::MENU_BOTTOM,  'label' => __('Under Menu')],
            ['value' => self::CONTENT_TOP,  'label' => __('Above Page Content')],
            ['value' => self::PAGE_BOTTOM,  'label' => __('Under Page Content')]
        ];
    }
}
