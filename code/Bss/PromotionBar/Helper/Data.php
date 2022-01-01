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

namespace Bss\PromotionBar\Helper;

/**
 * Class Data
 * @package Bss\PromotionBar\Helper
 */
class Data extends \Magento\Framework\App\Helper\AbstractHelper
{
    /**
     * Promotion Bar
     *
     * @var \Bss\PromotionBar\Model\ResourceModel\Bar
     */
    protected $promotionBar;

    /**
     * Date Time
     *
     * @var \Magento\Framework\Stdlib\DateTime\TimezoneInterface
     */
    protected $dateTime;

    /**
     * Scope Config
     *
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * Customer Session
     *
     * @var \Magento\Customer\Model\Session
     */
    protected $customerSession;

    /**
     * Constructor
     *
     * @param \Magento\Customer\Model\Session $customerSession
     * @param \Magento\Framework\Stdlib\DateTime\TimezoneInterface $datetime
     * @param \Bss\PromotionBar\Model\ResourceModel\Bar $promotionBar
     * @param \Magento\Framework\App\Helper\Context $context
     */
    public function __construct(
        \Magento\Customer\Model\SessionFactory $customerSession,
        \Magento\Framework\Stdlib\DateTime\TimezoneInterface $datetime,
        \Bss\PromotionBar\Model\ResourceModel\Bar $promotionBar,
        \Magento\Framework\App\Helper\Context $context
    ) {
        $this->scopeConfig = $context->getScopeConfig();
        $this->customerSession = $customerSession;
        $this->dateTime = $datetime;
        $this->promotionBar = $promotionBar;
        parent::__construct($context);
    }

    /**
     * Get Promotion Bar
     *
     * @param int $page
     * @param int $position
     * @return array
     */
    public function getPromotionBar($page, $position)
    {
        $bars = $this->promotionBar->getPromotionBarByPositionAndDate($position);
        $bars = $this->removeOtherPageBar($page, $bars);
        $bars = $this->removePromotionBarExpired($bars);
        return $bars;
    }

    /**
     * Remove Popup Expired
     *
     * @param array $bars
     * @return mixed
     */
    public function removePromotionBarExpired($bars)
    {
        $date = $this->dateTime->date()->format('Y-m-d H:i:s');
        foreach ($bars as $key => $value) {
            if ((!empty($value['display_to']) && strtotime($value['display_to']) <= strtotime($date))
                || (!empty($value['display_from']) && strtotime($value['display_from']) >= strtotime($date))
            ) {
                unset($bars[$key]);
            }
        }
        return $bars;
    }

    /**
     * Remove Bar In Other Page
     *
     * @param string $page
     * @param array $bars
     * @return array|string
     */
    public function removeOtherPageBar($page, $bars)
    {
        foreach ($bars as $key => $value) {
            $pages = explode(",", $value['page_display']);
            if (!in_array($page, $pages)
            ) {
                unset($bars[$key]);
            }
        }
        return $bars;
    }

    /**
     * Get Customer Close Bar Config
     *
     * @return mixed
     */
    public function getCustomerCloseConfig()
    {
        $storeScope = \Magento\Store\Model\ScopeInterface::SCOPE_STORE;
        return $this->scopeConfig->getValue('promotion_bar/general/customer_close', $storeScope);
    }

    /**
     * Get Multi Bar Config
     *
     * @return mixed
     */
    public function getMultiBarConfig()
    {
        $storeScope = \Magento\Store\Model\ScopeInterface::SCOPE_STORE;
        return $this->scopeConfig->getValue('promotion_bar/general/multi_bar', $storeScope);
    }

    /**
     * Get Slide Pager Config
     *
     * @return mixed
     */
    public function getSlidePager()
    {
        $storeScope = \Magento\Store\Model\ScopeInterface::SCOPE_STORE;
        return $this->scopeConfig->getValue('promotion_bar/general/slide_pager', $storeScope);
    }

    /**
     * Get Slide Control Config
     *
     * @return mixed
     */
    public function getSlideControl()
    {
        $storeScope = \Magento\Store\Model\ScopeInterface::SCOPE_STORE;
        return $this->scopeConfig->getValue('promotion_bar/general/slide_control', $storeScope);
    }

    /**
     * Get Slide Transition Time Config
     *
     * @return string
     */
    public function getSlideTransitionTime()
    {
        $storeScope = \Magento\Store\Model\ScopeInterface::SCOPE_STORE;
        return $this->scopeConfig->getValue('promotion_bar/general/slide_time', $storeScope);
    }

    /**
     * Get Slide Transition Time Config
     *
     * @return string
     */
    public function getSlideAutoHide()
    {
        $storeScope = \Magento\Store\Model\ScopeInterface::SCOPE_STORE;
        return $this->scopeConfig->getValue('promotion_bar/general/slide_auto_hide', $storeScope);
    }

    /**
     * Get Customer Group Id
     *
     * @return int
     */
    public function getCustomerGroupId()
    {
        $customerSessionCurrent = $this->customerSession->create();
        if ($customerSessionCurrent->isLoggedIn()) {
            $groupId = $customerSessionCurrent->getCustomer()->getGroupId();
            return $groupId;
        }
        return 0;
    }
}
