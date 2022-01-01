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

use Bss\RewardPoint\Api\Data\EarnPointInterfaceFactory;
use Bss\RewardPoint\Helper\Data;
use Magento\Quote\Api\CartTotalRepositoryInterface;
use Magento\Quote\Api\PaymentMethodManagementInterface;
use Bss\RewardPoint\Api\Data\UpdateItemDetailsInterfaceFactory;
use Magento\Quote\Model\Quote\Address\Total;
use Magento\Quote\Model\QuoteRepository;
use Magento\Store\Model\StoreManagerInterface;

/**
 * Class UpdateQuoteTotal
 *
 * @package Bss\RewardPoint\Model
 */
class UpdateQuoteTotal implements \Bss\RewardPoint\Api\QuoteTotalsInterface
{
    /**
     * @var QuoteRepository
     */
    protected $quote;

    /**
     * @var Total
     */
    protected $total;

    /**
     * @var UpdateItemDetailsInterfaceFactory
     */
    private $updateItemDetails;

    /**
     * @var PaymentMethodManagementInterface
     */
    private $paymentMethodManagement;

    /**
     * @var CartTotalRepositoryInterface
     */
    private $cartTotalRepository;

    /**
     * @var EarnPointInterfaceFactory
     */
    private $earnPointInterfaceFactory;

    /**
     * @var Data
     */
    private $helper;

    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * UpdateQuoteTotal constructor.
     * @param PaymentMethodManagementInterface $paymentMethodManagement
     * @param CartTotalRepositoryInterface $cartTotalRepository
     * @param EarnPointInterfaceFactory $earnPointInterfaceFactory
     * @param UpdateItemDetailsInterfaceFactory $updateItemDetails
     * @param Total $total
     * @param QuoteRepository $quote
     * @param Data $helper
     * @param StoreManagerInterface $storeManager
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        PaymentMethodManagementInterface $paymentMethodManagement,
        CartTotalRepositoryInterface $cartTotalRepository,
        EarnPointInterfaceFactory $earnPointInterfaceFactory,
        UpdateItemDetailsInterfaceFactory $updateItemDetails,
        Total $total,
        QuoteRepository $quote,
        Data $helper,
        StoreManagerInterface $storeManager
    ) {
        $this->helper = $helper;
        $this->storeManager = $storeManager;
        $this->quote = $quote;
        $this->total = $total;
        $this->updateItemDetails = $updateItemDetails;
        $this->earnPointInterfaceFactory = $earnPointInterfaceFactory;
        $this->paymentMethodManagement = $paymentMethodManagement;
        $this->cartTotalRepository = $cartTotalRepository;
    }

    /**
     * Update Totals from cart detail
     *
     * @param int $quoteId
     * @return \Bss\RewardPoint\Api\Data\UpdateItemDetailsInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function update($quoteId)
    {
        $quote = $this->quote->get($quoteId);
        $paymentMethods = $this->paymentMethodManagement->getList($quoteId);
        $shippingMethods = $quote->getShippingAddress()->getShippingMethod();
        $totals = $this->cartTotalRepository->get($quoteId);
        $cartDetails = $this->updateItemDetails->create();
        $cartDetails->setShippingMethods($shippingMethods);
        $cartDetails->setPaymentMethods($paymentMethods);
        $cartDetails->setTotals($totals);
        return $cartDetails;
    }

    /**
     * Apply Earn point to quote
     *
     * @param int $quoteId
     * @return \Bss\RewardPoint\Api\Data\EarnPointInterface
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function applyEarnPoints($quoteId)
    {
        $quote = $this->quote->get($quoteId);
        $store = $this->storeManager->getStore($quote->getStoreId());
        $websiteId = $store->getWebsiteId();

        $postEarnPoint = $this->earnPointInterfaceFactory->create();
        if (!$quote->getCustomer()->getId() || $quote->getIsMultiShipping() || !$this->helper->isActive($websiteId)) {
            $postEarnPoint->setStatus(false);
            $postEarnPoint->setEarnPoint(0);
            return $postEarnPoint;
        }

        if (!$this->helper->isEarnOrderPaidbyPoint($websiteId) && $quote->getSpendPoints() > 0) {
            $postEarnPoint->setStatus(false);
            $postEarnPoint->setEarnPoint(0);
            return $postEarnPoint;
        }

        $points = $quote->getEarnPoints();
        $postEarnPoint->setEarnPoint($points);
        $postEarnPoint->setStatus(true);
        return $postEarnPoint;
    }
}
