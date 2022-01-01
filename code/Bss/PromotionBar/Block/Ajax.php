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
 * Class Ajax
 * @package Bss\PromotionBar\Block
 */
class Ajax extends \Magento\Framework\View\Element\Template
{
    /**
     * Block Position
     *
     * @var int
     */
    protected $blockPositionAfter;

    /**
     * Block Type Page
     *
     * @var int
     */
    protected $blockType;

    /**
     * Block Position
     *
     * @var int
     */
    protected $blockPosition;

    /**
     * Block Page
     *
     * @var int
     */
    protected $blockPage;

    /**
     * Helper
     *
     * @var \Bss\PromotionBar\Helper\Data
     */
    protected $helper;

    /**
     * Store View Id
     *
     * @var int
     */
    protected $storeViewId;

    /**
     * Customer Group Id
     *
     * @var int
     */
    protected $customerGroupId;

    /**
     * Product Id
     *
     * @var int
     */
    protected $productId;

    /**
     * Category Id
     *
     * @var int
     */
    protected $categoryId;

    /**
     * Filter Provider
     *
     * @var \Magento\Cms\Model\Template\FilterProvider
     */
    protected $filterProvider;

    /**
     * @var \Magento\Framework\Session\SessionManagerInterface
     */
    protected $session;

    /**
     * @var \Magento\Framework\Serialize\Serializer\Json
     */
    protected $serializeJson;

    /**
     * Ajax constructor.
     * @param \Magento\Cms\Model\Template\FilterProvider $filterProvider
     * @param \Bss\PromotionBar\Helper\Data $helper
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Magento\Framework\Session\SessionManagerInterface $session
     * @param \Magento\Framework\Serialize\Serializer\Json $serializeJson
     * @param array $data
     */
    public function __construct(
        \Magento\Cms\Model\Template\FilterProvider $filterProvider,
        \Bss\PromotionBar\Helper\Data $helper,
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Framework\Session\SessionManagerInterface $session,
        \Magento\Framework\Serialize\Serializer\Json $serializeJson,
        array $data = []
    ) {
        $this->filterProvider = $filterProvider;
        $this->helper = $helper;
        $this->session = $session;
        $this->serializeJson = $serializeJson;
        parent::__construct($context, $data);
    }

    /**
     * Get Block Page
     *
     * @return int
     */
    public function getBlockPage()
    {
        return $this->blockPage;
    }

    /**
     * Set Block Page
     *
     * @param int $blockPage
     * @return void
     */
    public function setBlockPage($blockPage)
    {
        $this->blockPage = $blockPage;
    }

    /**
     * Get Block Position
     *
     * @return int
     */
    public function getBlockPosition()
    {
        return $this->blockPosition;
    }

    /**
     * Set Block Position
     *
     * @param int $blockPosition
     * @return void
     */
    public function setBlockPosition($blockPosition)
    {
        $this->blockPosition = $blockPosition;
    }

    /**
     * Get Promotion Bar
     *
     * @return array
     */
    public function getPromotionBar()
    {
        $promotionBars = $this->getAllPromotionBars();
        $bar = [];
        $maxPriority = 0;
        foreach ($promotionBars as $value) {
            if ($value['priority'] >= $maxPriority) {
                $maxPriority = $value['priority'];
                $bar = $value;
            }
        }
        return $bar;
    }

    /**
     * Get Multi Promotion Bars
     *
     * @return array
     */
    public function getAllPromotionBars()
    {
        $promotionBars = $this->helper->getPromotionBar($this->blockPage, $this->blockPosition);
        foreach ($promotionBars as $key => $value) {
            if (!$value['status']
                || !$this->inStoreView($value['bar_storeview'], $this->storeViewId)
                || !$this->inCustomerGroup($value['customer_group'], $this->customerGroupId)
                || $this->excludeProduct($value['exclude_product'], $this->productId)
                || $this->excludeCategory($value['exclude_category'], $this->categoryId)
            ) {
                unset($promotionBars[$key]);
            }
        }
        return $promotionBars;
    }

    /**
     * Set Store View Id
     *
     * @param int $storeViewId
     * @return void
     */
    public function setStoreViewId($storeViewId)
    {
        $this->storeViewId = $storeViewId;
    }

    /**
     * Set Customer Group Id
     *
     * @param int $customerGroupId
     * @return void
     */
    public function setCustomerGroupId($customerGroupId)
    {
        $this->customerGroupId = $customerGroupId;
    }

    /**
     * Check Attachment In Store View
     *
     * @param String $barStoreView
     * @param String $storeId
     * @return bool
     * @internal param String $attachmentStoreView
     */
    public function inStoreView($barStoreView, $storeId)
    {
        $listStoreView = explode(",", $barStoreView);
        if (in_array($storeId, $listStoreView)
            || in_array(0, $listStoreView)
        ) {
            return true;
        }
        return false;
    }

    /**
     * Check Attachment In Customer Group
     *
     * @param String $barCustomerGroup
     * @param String $customerGroupId
     * @return bool
     * @internal param String $attachmentCustomerGroup
     */
    public function inCustomerGroup($barCustomerGroup, $customerGroupId)
    {
        $listCustomerGroup = explode(",", $barCustomerGroup);
        if (in_array($customerGroupId, $listCustomerGroup)) {
            return true;
        }
        return false;
    }

    /**
     * Check Color Code
     *
     * @param string $colorCode
     * @return string
     */
    public function checkColorHex($colorCode)
    {
        // If user accidentally passed along the # sign, strip it off
        $colorCode = ltrim($colorCode, '#');

        if (ctype_xdigit($colorCode) &&
            (strlen($colorCode) == 6 || strlen($colorCode) == 3)
        ) {
            return "#" . $colorCode;
        }

        return "#fff";
    }

    /**
     * Set Product Id
     *
     * @param int $productId
     * @return void
     */
    public function setProductId($productId)
    {
        $this->productId = $productId;
    }

    /**
     * Set Category Id
     *
     * @param int $categoryId
     * @return void
     */
    public function setCategoryId($categoryId)
    {
        $this->categoryId = $categoryId;
    }

    /**
     * Check Exclude Products
     *
     * @param String $listProduct
     * @param int $productId
     * @return bool
     */
    public function excludeProduct($listProduct, $productId)
    {
        $listProduct = explode(",", $listProduct);
        if ($productId != 0 && in_array($productId, $listProduct)) {
            return true;
        }
        return false;
    }

    /**
     * Check Exclude Category
     *
     * @param string $listCategory
     * @param int $categoryId
     * @return bool
     */
    public function excludeCategory($listCategory, $categoryId)
    {
        $listCategory = explode(",", $listCategory);
        if ($categoryId != 0 && in_array($categoryId, $listCategory)) {
            return true;
        }
        return false;
    }

    /**
     * Get Helper
     *
     * @return \Bss\PromotionBar\Helper\Data
     */
    public function getHelper()
    {
        return $this->helper;
    }

    /**
     * Filter Content
     *
     * @param string $stringContent
     * @return string
     * @throws \Exception
     */
    public function filterContent($stringContent)
    {
        return $this->filterProvider->getPageFilter()->filter($stringContent);
    }

    /**
     * Set End Date Session
     */
    public function setEndDateSession()
    {
        if ($this->helper->getMultiBarConfig() && count($this->getAllPromotionBars()) != 1) {
            $promotionBars = $this->getAllPromotionBars();
        } else {
            $promotionBars = $this->getPromotionBar();
        }

        $endDate = $this->getEndDate($promotionBars);
        if (!empty($endDate)) {
            $this->session->start();
            $this->session->setBarEndDate($this->serializeJson->serialize($endDate));
        } else {
            $this->session->unsBarEndDate();
        }
    }

    /**
     * Get End Date Session
     *
     * @return mixed
     */
    public function getEndDateSession()
    {
        $this->session->start();
        return $this->session->getBarEndDate();
    }

    /**
     * Get End Date
     *
     * @param array $promotionBars
     * @return array
     */
    protected function getEndDate($promotionBars)
    {
        $endDate = [];
        foreach ($promotionBars as $bar) {
            if (!empty($bar['display_to'])) {
                $data['bar_id'] = $bar['bar_id'];
                $data['end_date'] = $bar['display_to'];
                $endDate[] = $data;
            }
        }
        return $endDate;
    }
}
