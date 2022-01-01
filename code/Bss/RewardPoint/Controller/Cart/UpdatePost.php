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
namespace Bss\RewardPoint\Controller\Cart;

use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Checkout\Model\CartFactory;
use Bss\RewardPoint\Helper\Data;
use Magento\Quote\Api\CartRepositoryInterface;
use Magento\Framework\Pricing\PriceCurrencyInterface;

/**
 * Class UpdatePost
 *
 * @package Bss\RewardPoint\Controller\Cart
 */
class UpdatePost extends Action
{
    /**
     * @var \Magento\Framework\Controller\Result\JsonFactory
     */
    protected $resultJsonFactory;

    /**
     * @var CartFactory
     */
    protected $cartFactory;

    /**
     * @var Data
     */
    protected $helper;

    /**
     * @var CartRepositoryInterface
     */
    protected $quoteRepository;

    /**
     * @var PriceCurrencyInterface
     */
    protected $priceCurrency;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var \Bss\RewardPoint\Model\RateFactory
     */
    protected $rateFactory;

    /**
     * @var \Bss\RewardPoint\Model\TransactionFactory
     */
    protected $transactionFactory;

    /**
     * UpdatePost constructor.
     * @param Context $context
     * @param CartFactory $cartFactory
     * @param Data $helper
     * @param CartRepositoryInterface $quoteRepository
     * @param PriceCurrencyInterface $priceCurrency
     * @param \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Bss\RewardPoint\Model\RateFactory $rateFactory
     * @param \Bss\RewardPoint\Model\TransactionFactory $transactionFactory
     */
    public function __construct(
        Context $context,
        CartFactory $cartFactory,
        Data $helper,
        CartRepositoryInterface $quoteRepository,
        PriceCurrencyInterface $priceCurrency,
        \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Bss\RewardPoint\Model\RateFactory $rateFactory,
        \Bss\RewardPoint\Model\TransactionFactory $transactionFactory
    ) {
        parent::__construct($context);
        $this->cartFactory = $cartFactory;
        $this->helper = $helper;
        $this->quoteRepository = $quoteRepository;
        $this->priceCurrency = $priceCurrency;
        $this->resultJsonFactory = $resultJsonFactory;
        $this->storeManager = $storeManager;
        $this->rateFactory = $rateFactory;
        $this->transactionFactory = $transactionFactory;
    }

    /**
     * @return $this|\Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\ResultInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function execute()
    {
        /** @var \Magento\Framework\Controller\Result\Json $resultJson */
        $resultJson = $this->resultJsonFactory->create();

        $spendPoint = (int) $this->getRequest()->getParam('spend_reward_point');
        $quote = $this->cartFactory->create()->getQuote();

        $store = $this->storeManager->getStore($quote->getStoreId());
        $websiteId = $store->getWebsiteId();
        $customerId = $quote->getCustomerId();
        $customerGroupId = $quote->getCustomerGroupId();

        $totalPoints = $this->transactionFactory->create()->loadByCustomer($customerId, $websiteId)->getPointBalance();

        $rate = $this->rateFactory->create()->fetch(
            $customerGroupId,
            $websiteId
        );

        $maximumSpendPoint = (int)$this->helper->getMaximumPointCanSpendPerOrder();
        $response['message'] = __('Successfully!');
        $response['status_message']  = 'success';
        if ($spendPoint < 0 || !$quote->getId()) {
            $response['message'] = __('Something went wrong. Please enter a value again');
            $response['status_message']  = 'error';
            $response['status']  = true;
        } elseif ($spendPoint == 0) {
            $quote->setSpendPoints($spendPoint);
            $quote->collectTotals();
            $quote->save();
            $response['message'] = __('Successfully cancel!');
            $response['status'] = true;
            $response['spend_point'] = 0;
            $response['amount'] = 0;
            $response['pointLeft'] = $totalPoints;
        } else {
            if ($maximumSpendPoint > 0 && $spendPoint > $maximumSpendPoint) {
                $response['message'] = __("You can't use more reward point than you have");
                $response['status_message']  = 'warning';
                $spendPoint = $maximumSpendPoint;
            }

            if ($spendPoint > $totalPoints) {
                $response['message'] = __('You don’t have enough reward points. Earn more!.');
                $response['status_message']  = 'warning';
                $spendPoint = $totalPoints;
            }

            $baseAmount = $this->priceCurrency->round($spendPoint/$rate->getBasecurrencyToPointRate());

            $quote->setSpendPoints($spendPoint);
            $quote->collectTotals();
            $quote->save();
            $baseRwpAmount = $this->priceCurrency->round($quote->getBaseRwpAmount());

            if ($baseAmount > $baseRwpAmount) {
                $response['status_message']  = 'warning';
                $response['message'] = __("You can't use more reward point than the order amount.");
            }

            $spendPoint = $quote->getSpendPoints();
            $pointLeft = $totalPoints - $spendPoint;
            $response['status'] = true;
            $response['spend_point'] = $spendPoint;
            $response['amount'] = $quote->getRwpAmount();
            $response['pointLeft'] = $pointLeft;
        }

        return $resultJson->setData($response);
    }
}
