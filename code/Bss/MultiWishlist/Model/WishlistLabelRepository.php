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
namespace Bss\MultiWishlist\Model;

use Bss\MultiWishlist\Api\Data\MultiwishlistInterface;
use Bss\MultiWishlist\Api\Data\MultiwishlistInterfaceFactory;
use Bss\MultiWishlist\Api\MultiwishlistRepositoryInterface;
use Magento\Framework\Exception\NoSuchEntityException;

/**
 * Class WishlistLabelRepository
 *
 * @package Bss\MultiWishlist\Model
 */
class WishlistLabelRepository implements MultiwishlistRepositoryInterface
{
    /**
     * Instances
     *
     * @var array
     */
    protected $instances = [];

    /**
     * @var ResourceModel\WishlistLabel
     */
    protected $resource;

    /**
     * @var MultiwishlistInterfaceFactory
     */
    protected $mWishlistIterfaceFactory;

    /**
     * @var WishlistLabelFactory
     */
    protected $wishlistLabelFactory;

    /**
     * WishlistLabelRepository constructor.
     * @param ResourceModel\WishlistLabel $resource
     * @param MultiwishlistInterfaceFactory $mWishlistIterfaceFactory
     * @param WishlistLabelFactory $wishlistLabelFactory
     */
    public function __construct(
        ResourceModel\WishlistLabel $resource,
        MultiwishlistInterfaceFactory $mWishlistIterfaceFactory,
        WishlistLabelFactory $wishlistLabelFactory
    ) {
        $this->resource = $resource;
        $this->mWishlistIterfaceFactory = $mWishlistIterfaceFactory;
        $this->wishlistLabelFactory = $wishlistLabelFactory;
    }

    /**
     * Get multi wish list by id
     *
     * @param int $wishlistId
     * @return MultiwishlistInterface|mixed
     * @throws NoSuchEntityException
     */
    public function getById($wishlistId)
    {
        if (!isset($this->instances[$wishlistId])) {
            $mWishlist = $this->wishlistLabelFactory->create();
            $this->resource->load($mWishlist, $wishlistId);
            if (!$mWishlist->getId()) {
                throw new NoSuchEntityException(__('Wish list with id "%1" does not exist.', $wishlistId));
            }
            $this->instances[$wishlistId] = $mWishlist;
        }
        return $this->instances[$wishlistId];
    }

    /**
     * Get Wishlist Label
     *
     * @param int $wishlistId
     * @param int $storeId
     * @return mixed
     * @throws NoSuchEntityException
     */
    public function get($wishlistId, $storeId = null)
    {
        $cacheKey = 'all';
        if ($storeId) {
            $cacheKey = $storeId;
        }
        if (!isset($this->instances[$wishlistId][$cacheKey])) {
            $mWishlist = $this->wishlistLabelFactory->create();

            $this->resource->load($mWishlist, $wishlistId);
            if (!$mWishlist->getId()) {
                throw NoSuchEntityException::singleField('id', $wishlistId);
            }
            $this->instances[$wishlistId][$cacheKey] = $mWishlist;
        }
        return $this->instances[$wishlistId][$cacheKey];
    }
}
