<?php
/**
 * BSS Commerce Co.
 *
 * NOTICE OF LICENSE
 *
 * @category   BSS
 * @package    Bss_MultiWishlist
 * @author     Extension Team
 * @copyright  Copyright (c) 2018-2019 BSS Commerce Co. ( http://bsscommerce.com )
 * @license    http://bsscommerce.com/Bss-Commerce-License.txt
 */
namespace Bss\MultiWishlist\Api;

/**
 * Interface MultiwishlistRepositoryInterface
 *
 * @package Bss\MultiWishlist\Api
 * @api
 */
interface MultiwishlistRepositoryInterface
{
    /**
     * Get wishlist from id
     *
     * @param int $wishlistId
     * @return Data\MultiwishlistInterface
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getById($wishlistId);

    /**
     * Get wishlist
     *
     * @param int $wishlistId
     * @param int $storeId
     * @return mixed
     */
    public function get($wishlistId, $storeId = null);
}
