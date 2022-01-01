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
 * @package    Bss_StoreCredit
 * @author     Extension Team
 * @copyright  Copyright (c) 2017-2018 BSS Commerce Co. ( http://bsscommerce.com )
 * @license    http://bsscommerce.com/Bss-Commerce-License.txt
 */

namespace Bss\StoreCredit\Observer;

use Magento\Framework\Event\ObserverInterface;
use Bss\StoreCredit\Model\CreditFactory;
use Bss\StoreCredit\Helper\Data;
use Bss\StoreCredit\Api\StoreCreditRepositoryInterface;
use Psr\Log\LoggerInterface;
use Bss\StoreCredit\Model\HistoryFactory;
use Bss\StoreCredit\Model\History;
use Magento\Framework\Event\Observer;

/**
 * Class RefundOrderStoreCredit
 * @package Bss\StoreCredit\Observer
 */
class CancelOrderStoreCreditAfter implements ObserverInterface
{
    /**
     * @var \Bss\StoreCredit\Helper\Data
     */
    private $bssStoreCreditHelper;

    /**
     * @var \Bss\StoreCredit\Model\CreditFactory
     */
    private $creditFactory;

    /**
     * @var \Bss\StoreCredit\Api\StoreCreditRepositoryInterface
     */
    private $storeCreditRepository;

    /**
     * @var \Bss\StoreCredit\Model\HistoryFactory
     */
    private $historyFactory;

    /**
     * @var \Psr\Log\LoggerInterface
     */
    private $logger;

    /**
     * CancelOrderStoreCreditAfter constructor.
     * @param CreditFactory $creditFactory
     * @param Data $bssStoreCreditHelper
     * @param StoreCreditRepositoryInterface $storeCreditRepository
     * @param HistoryFactory $historyFactory
     * @param LoggerInterface $logger
     */
    public function __construct(
        CreditFactory $creditFactory,
        Data $bssStoreCreditHelper,
        StoreCreditRepositoryInterface $storeCreditRepository,
        HistoryFactory $historyFactory,
        LoggerInterface $logger
    ) {
        $this->creditFactory = $creditFactory;
        $this->bssStoreCreditHelper = $bssStoreCreditHelper;
        $this->storeCreditRepository = $storeCreditRepository;
        $this->historyFactory = $historyFactory;
        $this->logger = $logger;
    }

    /**
     * @param \Magento\Framework\Event\Observer $observer
     * @return void
     */
    public function execute(Observer $observer)
    {

        $order = $observer->getEvent()->getOrder();
        try {
            $customerId = $order->getCustomerId();
            $websiteId = $order->getStore()->getWebsiteId();
            $credit = $this->storeCreditRepository->get($customerId, $websiteId);
            $creditModel = $this->creditFactory->create();
            $baseStorecredit = $order->getBaseBssStorecreditAmount();
            $historyModel = $this->historyFactory->create();

            if (!$baseStorecredit) {
                return;
            }
            if ($credit->getId()) {
                $baseAmountUpdate = $credit->getBalanceAmount() + $baseStorecredit;
                $credit->setBalanceAmount($baseAmountUpdate)
                    ->save();
            } else {
                $creditModel->setCustomerId($customerId)
                    ->setBalanceAmount($baseStorecredit)
                    ->setWebsiteId($websiteId)
                    ->save();
            }
            $data = [
                'customer_id' => $customerId,
                'order_id' => $order->getId(),
                'website_id' => $websiteId,
                'type' => History::TYPE_CANCEL,
                'change_amount' => $baseStorecredit,
                'balance_amount' => $baseAmountUpdate,
                'comment_content' => null,
                'is_notified' => true,
            ];

            $historyModel->updateHistory($data, $order->getStoreId());
        } catch (\Exception $e) {
            $this->logger->error($e->getMessage());
        }
    }
}
