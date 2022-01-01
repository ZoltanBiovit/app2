<?php
/**
 * BSS Commerce Co.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the EULA
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at thisURL:
 * http://bsscommerce.com/Bss-Commerce-License.txt
 *
 * @category   BSS
 * @package    Bss_SizeChart
 * @author     Extension Team
 * @copyright  Copyright (c) 2017-2018 BSS Commerce Co. ( http://bsscommerce.com )
 * @license    http://bsscommerce.com/Bss-Commerce-License.txt
 */

namespace Bss\PromotionBar\Observer;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;

/**
 * Class EditLayout
 * @package Bss\PromotionBar\Observer
 */
class EditLayout implements ObserverInterface
{
    /**
     * @var \Magento\Framework\View\LayoutFactory
     */
    protected $layoutFactory;

    /**
     * @var \Bss\PromotionBar\Helper\Data
     */
    protected $helper;

    /**
     * EditLayout constructor.
     * @param \Magento\Framework\View\LayoutFactory $layoutFactory
     * @param \Bss\PromotionBar\Helper\Data $helper
     */
    public function __construct(
        \Magento\Framework\View\LayoutFactory $layoutFactory,
        \Bss\PromotionBar\Helper\Data $helper
    ) {
        $this->layoutFactory = $layoutFactory;
        $this->helper = $helper;
    }

    /**
     * Check tab information
     *
     * @param Observer $observer
     * @return void
     */
    public function execute(Observer $observer)
    {
        $array = [
            'promotionbar_home_page_top' => 1,
            'promotionbar_home_menu_top' => 2,
            'promotionbar_home_menu_bottom' => 3,
            'promotionbar_home_content_top' => 4,
            'promotionbar_home_page_bottom' => 5,
            'promotionbar_category_page_top' => 1,
            'promotionbar_category_menu_top' => 2,
            'promotionbar_category_menu_bottom' => 3,
            'promotionbar_category_content_top' => 4,
            'promotionbar_category_page_bottom' => 5,
            'promotionbar_product_page_top' => 1,
            'promotionbar_product_menu_top' => 2,
            'promotionbar_product_menu_bottom' => 3,
            'promotionbar_product_content_top' => 4,
            'promotionbar_product_page_bottom' => 5,
            'promotionbar_checkout_page_top' => 1,
            'promotionbar_checkout_content_top' => 4,
            'promotionbar_checkout_page_bottom' => 5,
            'promotionbar_cart_page_top' => 1,
            'promotionbar_cart_menu_top' => 2,
            'promotionbar_cart_menu_bottom' => 3,
            'promotionbar_cart_content_top' => 4,
            'promotionbar_cart_page_bottom' => 5,
            'promotionbar_default_page_top' => 1,
            'promotionbar_default_menu_top' => 2,
            'promotionbar_default_menu_bottom' => 3,
            'promotionbar_default_content_top' => 4,
            'promotionbar_default_page_bottom' => 5
        ];
        if (isset($array[$observer->getElementName()])) {
            $position = $array[$observer->getElementName()];
        } else {
            $position = 0;
        }
        if ($position) {
            $blockPromotionBar = $observer->getLayout()->getBlock($observer->getElementName());
            $page = $blockPromotionBar->getBlockType();

            $storeViewId = $blockPromotionBar->getStoreId();
            $productId = $blockPromotionBar->getProductId();
            $categoryId = $blockPromotionBar->getCategoryId();

            $customerGroup = $this->helper->getCustomerGroupId();

            $layout = $this->layoutFactory->create();
            $block = $layout->createBlock(\Bss\PromotionBar\Block\Ajax::class);
            $block->setBlockPage($page);
            $block->setBlockPosition($position);
            $block->setStoreViewId($storeViewId);
            $block->setCustomerGroupId($customerGroup);
            $block->setProductId($productId);
            $block->setCategoryId($categoryId);
            $block->setEndDateSession();
            $block->setTemplate('Bss_PromotionBar::ajax/promotionbar.phtml');
            $html = $observer->getTransport()->getOutput() . $block->toHtml();
            $observer->getTransport()->setOutput($html);
        }
    }
}
