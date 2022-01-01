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
 * @package    Bss_StoreCredit
 * @author     Extension Team
 * @copyright  Copyright (c) 2017-2018 BSS Commerce Co. ( http://bsscommerce.com )
 * @license    http://bsscommerce.com/Bss-Commerce-License.txt
 */

namespace Bss\StoreCredit\Api;

/**
 * @api
 */
interface StoreCreditRepositoryInterface
{
    /**
     * Retrieve customer.
     *
     * @param int|null $customerId
     * @param int|null $websiteId
     * @return $this
     */
    public function get($customerId = null, $websiteId = null);

    /**
     * Apply store credit
     *
     * @param float $amount
     * @return $this
     */
    public function apply($amount);
}
