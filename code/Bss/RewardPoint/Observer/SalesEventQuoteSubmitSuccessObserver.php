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
namespace Bss\RewardPoint\Observer;

use Magento\Framework\Event\ObserverInterface;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\Event\Observer;
use Bss\RewardPoint\Model\Config\Source\TransactionActions;

class SalesEventQuoteSubmitSuccessObserver implements ObserverInterface
{
    /**
     * @var \Bss\RewardPoint\Helper\RewardCustomAction
     */
    protected $helperCustomAction;

    /**
     * @var \Bss\RewardPoint\Model\TransactionFactory
     */
    protected $transactionFactory;

    /**
     * @var \Bss\RewardPoint\Helper\Data
     */
    protected $helper;

    /**
     * @var StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var \Bss\RewardPoint\Model\RateFactory
     */
    protected $rateFactory;

    /**
     * @var \Psr\Log\LoggerInterface
     */
    protected $logger;
    /**
     * SalesEventQuoteSubmitSuccessObserver constructor.
     * @param \Bss\RewardPoint\Helper\Data $helper
     * @param \Bss\RewardPoint\Helper\RewardCustomAction $helperCustomAction
     * @param \Bss\RewardPoint\Model\TransactionFactory $transactionFactory
     * @param \Bss\RewardPoint\Model\RateFactory $rateFactory
     * @param StoreManagerInterface $storeManager
     * @param \Magento\Quote\Model\Quote\AddressFactory $quoteAddressFactory
     * @param \Psr\Log\LoggerInterface $logger
     */
    public function __construct(
        \Bss\RewardPoint\Helper\Data $helper,
        \Bss\RewardPoint\Helper\RewardCustomAction $helperCustomAction,
        \Bss\RewardPoint\Model\TransactionFactory $transactionFactory,
        \Bss\RewardPoint\Model\RateFactory $rateFactory,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Quote\Model\Quote\AddressFactory $quoteAddressFactory,
        \Psr\Log\LoggerInterface $logger
    ) {
        $this->helper              = $helper;
        $this->helperCustomAction  = $helperCustomAction;
        $this->transactionFactory  = $transactionFactory;
        $this->rateFactory  = $rateFactory;
        $this->storeManager        = $storeManager;
        $this->quoteAddressFactory = $quoteAddressFactory;
        $this->logger = $logger;
    }

    /**
     * @param Observer $observer
     * @return $this|void
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function execute(Observer $observer)
    {
        $order = $observer->getEvent()->getOrder();
        $quote = $observer->getEvent()->getQuote();

        if ($order->getCustomerIsGuest() || $order->isCanceled() || !$this->helper->isActive()) {
            return $this;
        }

        $options = [
                'customerId' => $order->getCustomerId(),
                'action_id'  => $order->getId(),
                'storeId'    => $order->getStoreId()
               ];

        $this->helperCustomAction->processCustomRule(TransactionActions::FIRST_ORDER, $options);

        $earn_point = $quote->getEarnPoints();
        $this->saveTransaction($quote, $order->getId(), $earn_point);

        $amount = $quote->getRwpAmount();
        $baseAmount = $quote->getBaseRwpAmount();
        if ($baseAmount && $amount) {
            $order->setRwpAmount($amount)
                ->setBaseRwpAmount($baseAmount)
                ->save();

            $spend_point = $quote->getSpendPoints()*(-1);
            $this->saveTransaction($quote, $order->getId(), $spend_point);
        }

        return $this;
    }

    /**
     * @param \Magento\Quote\Model\Quote $quote
     * @param int $orderId
     * @param int $point
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    protected function saveTransaction($quote, $orderId, $point)
    {
        if ((int)$point !== 0) {
            $transaction  = $this->transactionFactory->create();

            $customerId = $quote->getCustomerId();
            $websiteId = $this->storeManager->getStore($quote->getStoreId())->getWebsiteId();

            $data = [
                'website_id'   => $websiteId,
                'customer_id'  => $customerId,
                'point'        => $point,
                'action_id'    => $orderId,
                'action'       => TransactionActions::ORDER,
                'created_at'   => $this->helper->getCreateAt(),
                'created_by'   => $quote->getCustomerEmail()
            ];

            if ($point > 0) {
                $data['note']       = $quote->getRwpNote();
                $data['is_expired'] = (bool)$this->helper->getExpireDay($websiteId);
                $data['expires_at'] = $this->helper->getExpireDay($websiteId);
            } else {
                $customerGroupId = $quote->getCustomerGroupId();
                $rate = $this->rateFactory->create()->fetch(
                    $customerGroupId,
                    $websiteId
                );

                $data['amount'] = $quote->getBaseRwpAmount();
                $data['base_currrency_code'] = $quote->getBaseCurrencyCode();
                $data['basecurrency_to_point_rate'] = $rate->getBasecurrencyToPointRate();
            }

            try {
                $transaction->setData($data);
                if ($point < 0) {
                    $transaction->updatePointUsed();
                }
                $transaction->save();
            } catch (\Exception $e) {
                $this->logger->error($e->getMessage());
            }
        }
    }
}
