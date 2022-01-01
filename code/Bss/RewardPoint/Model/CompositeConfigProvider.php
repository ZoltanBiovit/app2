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
namespace Bss\RewardPoint\Model;

use Magento\Checkout\Model\ConfigProviderInterface;

class CompositeConfigProvider implements ConfigProviderInterface
{
    /**
     * @var \Bss\RewardPoint\Helper\Data
     */
    protected $helper;

    /**
     * @var \Magento\Framework\Pricing\Helper\Data
     */
    protected $priceHelper;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var RateFactory
     */
    protected $rateFactory;

    /**
     * @var \Magento\Checkout\Model\SessionFactory
     */
    protected $checkoutSession;

    /**
     * @var TransactionFactory
     */
    protected $transactionFactory;

    /**
     * CompositeConfigProvider constructor.
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Checkout\Model\SessionFactory $checkoutSession
     * @param \Magento\Framework\Pricing\Helper\Data $priceHelper
     * @param TransactionFactory $transactionFactory
     * @param RateFactory $rateFactory
     * @param \Bss\RewardPoint\Helper\Data $helper
     */
    public function __construct(
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Checkout\Model\SessionFactory $checkoutSession,
        \Magento\Framework\Pricing\Helper\Data $priceHelper,
        \Bss\RewardPoint\Model\TransactionFactory $transactionFactory,
        \Bss\RewardPoint\Model\RateFactory $rateFactory,
        \Bss\RewardPoint\Helper\Data $helper
    ) {
        $this->storeManager = $storeManager;
        $this->checkoutSession = $checkoutSession;
        $this->priceHelper = $priceHelper;
        $this->transactionFactory = $transactionFactory;
        $this->rateFactory = $rateFactory;
        $this->helper = $helper;
    }

    /**
     * @return array
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getConfig()
    {
        $output = [];
        $output['point_balance'] = $this->getPointBalance();
        $output['point_left'] = $this->getRewardPointsTotal();
        $output['spend_point'] = $this->getSpendPoints();
        $output['rateHtml'] = $this->getRateHtml();
        $output['display'] = $this->isDisplay();
        $output['pointSlider'] = $this->helper->isPointSlider();

        return $output;
    }

    /**
     * @return int
     */
    public function getRewardPointsTotal()
    {
        $spend_point = $this->getQuote()->getSpendPoints();
        $pointLeft = (int)$this->getPointBalance() - (int)$spend_point;
        return $pointLeft;
    }

    /**
     * @return int
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getPointBalance()
    {
        $transaction = $this->transactionFactory->create()
            ->loadByCustomer($this->getCustomerId(), $this->getWebsiteId());
        return $transaction->getPointBalance();
    }

    /**
     * @return \Magento\Framework\Phrase
     */
    public function getRateHtml()
    {
       return __('%1 point(s) can be redeemed for %2', $this->getRateCurrencytoPoint(), $this->getCurrency());
    }

    /**
     * @return float|string
     */
    public function getCurrency()
    {
        return $this->priceHelper->currency(1, true, false);
    }

    /**
     * @return float
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getRateCurrencytoPoint()
    {
        $rate = $this->rateFactory->create()->fetch(
            $this->getCustomerGroupId(),
            $this->getWebsiteId()
        );
        return round($rate->getBasecurrencyToPointRate());
    }

    /**
     * @return int
     */
    public function getSpendPoints()
    {
        return (int)$this->getQuote()->getSpendPoints();
    }

    /**
     * @return int
     */
    public function getCustomerGroupId()
    {
        return $this->getQuote()->getCustomerGroupId();
    }

    /**
     * @return int
     */
    public function getCustomerId()
    {
        return $this->getQuote()->getCustomerId();
    }

    /**
     * @return int
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getWebsiteId()
    {
        $store = $this->storeManager->getStore($this->getQuote()->getStoreId());
        return $store->getWebsiteId();
    }

    /**
     * @return \Magento\Quote\Model\Quote
     */
    public function getQuote()
    {
        return $this->checkoutSession->create()->getQuote();
    }

    /**
     * @return bool
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function isDisplay()
    {
        $minimum = (int)$this->helper->getPointsThreshold($this->getWebsiteId());
        if ($minimum <= (int)$this->getPointBalance() && $this->helper->isActive()) {
            return true;
        }
        return false;
    }
}
