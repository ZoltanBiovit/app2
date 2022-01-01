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

use Magento\Framework\Model\AbstractModel;

/**
 * Class WishlistLabel
 *
 * @package Bss\MultiWishlist\Model
 */
class WishlistLabel extends AbstractModel
{
    /**
     * Define resource model
     */
    protected function _construct()
    {
        $this->_init(\Bss\MultiWishlist\Model\ResourceModel\WishlistLabel::class);
    }
}
