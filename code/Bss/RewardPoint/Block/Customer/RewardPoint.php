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
namespace Bss\RewardPoint\Block\Customer;

use Magento\Framework\View\Element\Template\Context;
use Magento\Customer\Model\SessionFactory;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\Pricing\Helper\Data;
use Bss\RewardPoint\Model\Config\Source\TransactionActions;
use Bss\RewardPoint\Helper\InjectModel;

/**
 * Class RewardPoint
 *
 * @package Bss\RewardPoint\Block\Customer
 */
class RewardPoint extends \Bss\RewardPoint\Block\RewardPoint
{
    /**
     * RewardPoint constructor.
     * @param Context $context
     * @param SessionFactory $customerSession
     * @param StoreManagerInterface $storeManager
     * @param Data $priceHelper
     * @param TransactionActions $transactionActions
     * @param InjectModel $helperInject
     * @param array $data
     */
    public function __construct(
        Context $context,
        SessionFactory $customerSession,
        StoreManagerInterface $storeManager,
        Data $priceHelper,
        TransactionActions $transactionActions,
        InjectModel $helperInject,
        array $data = []
    ) {
        parent::__construct(
            $context,
            $customerSession,
            $storeManager,
            $priceHelper,
            $transactionActions,
            $helperInject,
            $data
        );
    }

    /**
     * Limit of Transactions
     */
    const TRANSACTION_LIMIT = 5;

    /**
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();
        $this->getRecentTransactions();
    }

    /**
     * Get recently transaction. By default they will be limited by 5.
     */
    private function getRecentTransactions()
    {
        $transactions = $this->getTransactionCollection()->setPageSize(self::TRANSACTION_LIMIT)->load();
        $this->setTransactions($transactions);
    }

    /**
     * @param int $transactionId
     * @return string
     */
    public function getViewUrl($transactionId)
    {
        return $this->getUrl('rewardpoint/transaction/index', ['id' => $transactionId]);
    }

    /**
     * @return \Bss\RewardPoint\Model\Notification
     */
    public function getNotify()
    {
        $customerId = $this->getCustomerId();
        return $this->helperInject->createNotificationModel()->load($customerId);
    }

    /**
     * @param string $transactionId
     * @return string
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getBalanceByTransaction($transactionId)
    {
        $lastestBalance = $this->helperInject->createTransactionResource()->getPointBalanceForGrid(
            $transactionId
        );
        return$lastestBalance;
    }
}
