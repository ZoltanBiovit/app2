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

namespace Bss\PromotionBar\Block;

use Bss\PromotionBar\Model\Source\Position;
use Bss\PromotionBar\Model\Source\PageDisplay;

/**
 * Class PromotionBar
 * @package Bss\PromotionBar\Block
 */
class PromotionBar extends \Magento\Framework\View\Element\Template
{
    /**
     * Block Position
     *
     * @var int
     */
    protected $blockPosition;

    /**
     * Block Type Page
     *
     * @var int
     */
    protected $blockType;

    /**
     * Store Manager
     *
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;

    /**
     * Core Registry
     *
     * @var \Magento\Framework\Registry
     */
    protected $coreRegistry;

    /**
     * Constructor.
     *
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\Registry $registry,
        \Magento\Framework\View\Element\Template\Context $context,
        array $data = []
    ) {

        $this->coreRegistry = $registry;
        $this->storeManager = $context->getStoreManager();
        parent::__construct($context, $data);
    }

    /**
     * Retrieve block position
     *
     * @return int
     */
    public function getBlockPosition()
    {
        if (false !== strpos($this->getNameInLayout(), 'page_top')) {
            $this->blockPosition = Position::PAGE_TOP;
        }
        if (false !== strpos($this->getNameInLayout(), 'menu_top')) {
            $this->blockPosition = Position::MENU_TOP;
        }
        if (false !== strpos($this->getNameInLayout(), 'menu_bottom')) {
            $this->blockPosition = Position::MENU_BOTTOM;
        }
        if (false !== strpos($this->getNameInLayout(), 'content_top')) {
            $this->blockPosition = Position::CONTENT_TOP;
        }
        if (false !== strpos($this->getNameInLayout(), 'page_bottom')) {
            $this->blockPosition = Position::PAGE_BOTTOM;
        }
        return $this->blockPosition;
    }

    /**
     * Retrieve block type
     *
     * @return int
     */
    public function getBlockType()
    {
        if (false !== strpos($this->getNameInLayout(), 'promotionbar_product')) {
            $this->blockType = PageDisplay::PRODUCT_PAGE;
        }
        if (false !== strpos($this->getNameInLayout(), 'promotionbar_category')) {
            $this->blockType = PageDisplay::CATEGORY_PAGE;
        }
        if (false !== strpos($this->getNameInLayout(), 'promotionbar_home')) {
            $this->blockType = PageDisplay::HOME_PAGE;
        }
        if (false !== strpos($this->getNameInLayout(), 'promotionbar_cart')) {
            $this->blockType = PageDisplay::CART_PAGE;
        }
        if (false !== strpos($this->getNameInLayout(), 'promotionbar_checkout')) {
            $this->blockType = PageDisplay::CHECKOUT_PAGE;
        }
        if (false !== strpos($this->getNameInLayout(), 'promotionbar_default')) {
            $this->blockType = PageDisplay::ALL_OTHER_PAGE;
        }
        return $this->blockType;
    }

    /**
     * Get Store Id
     *
     * @return int
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getStoreId()
    {
        $storeId = $this->storeManager->getStore()->getId();
        return $storeId;
    }

    /**
     * Get Product Id
     *
     * @return int
     */
    public function getProductId()
    {
        $product = $this->coreRegistry->registry('product');
        $productId = (!empty($product)) ? $product->getId() : 0;
        return $productId;
    }

    /**
     * Get Category Id
     *
     * @return int
     */
    public function getCategoryId()
    {
        $category = $this->coreRegistry->registry('current_category');
        $categoryId = (!empty($category)) ? $category->getId() : 0;
        return $categoryId;
    }
}
