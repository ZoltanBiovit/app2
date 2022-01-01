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
 * @package    Bss_RewardPointGraphQl
 * @author     Extension Team
 * @copyright  Copyright (c) 2020 BSS Commerce Co. ( http://bsscommerce.com )
 * @license    http://bsscommerce.com/Bss-Commerce-License.txt
 */
namespace Bss\RewardPointGraphQl\Model\Resolver;

use Bss\RewardPoint\Block\Customer\RewardPoint;
use Bss\RewardPoint\Helper\RewardCustomAction;
use Bss\RewardPoint\Model\Config\Source\RuleType;
use Bss\RewardPoint\Model\Config\Source\TransactionActions;
use Bss\RewardPoint\Model\ResourceModel\Notification;
use Bss\RewardPoint\Model\ResourceModel\Transaction\CollectionFactory;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\GraphQl\Config\Element\Field;
use Magento\Framework\GraphQl\Query\Resolver\ContextInterface;
use Magento\Framework\GraphQl\Query\Resolver\Value;
use Magento\Framework\GraphQl\Query\ResolverInterface;
use Magento\Framework\GraphQl\Schema\Type\ResolveInfo;
use Magento\Framework\Registry;
use Magento\Store\Model\ScopeInterface;

/**
 * Class CustomerRewardPoint
 *
 * @package Bss\RewardPointGraphQl\Model\Resolver
 */
class CustomerRewardPoint implements ResolverInterface
{
    /**
     * @var RewardPoint
     */
    protected $rewardPoint;

    /**
     * @var Notification
     */
    protected $notification;

    /**
     * @var \Bss\RewardPoint\Block\Customer\PointShowing
     */
    protected $pointShowing;

    /**
     * @var \Magento\Newsletter\Model\Subscriber
     */
    protected $subscriber;

    /**
     * @var RewardCustomAction
     */
    protected $rewardActionHelper;

    /**
     * @var Registry
     */
    protected $registry;

    /**
     * @var CollectionFactory
     */
    protected $transactionFactory;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var \Magento\Customer\Model\CustomerFactory
     */
    protected $customerFactory;

    /**
     * @var \Bss\RewardPoint\Model\RuleFactory
     */
    protected $ruleFactory;

    /**
     * CustomerRewardPoint constructor.
     * @param \Bss\RewardPoint\Model\RuleFactory $ruleFactory
     * @param \Magento\Customer\Model\CustomerFactory $customerFactory
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param CollectionFactory $transactionFactory
     * @param Registry $registry
     * @param RewardCustomAction $rewardActionHelper
     * @param \Magento\Newsletter\Model\Subscriber $subscriber
     * @param \Bss\RewardPoint\Block\Customer\PointShowing $pointShowing
     * @param RewardPoint $rewardPoint
     * @param Notification $notification
     */
    public function __construct(
        \Bss\RewardPoint\Model\RuleFactory $ruleFactory,
        \Magento\Customer\Model\CustomerFactory $customerFactory,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        CollectionFactory $transactionFactory,
        Registry $registry,
        RewardCustomAction $rewardActionHelper,
        \Magento\Newsletter\Model\Subscriber $subscriber,
        \Bss\RewardPoint\Block\Customer\PointShowing $pointShowing,
        RewardPoint $rewardPoint,
        Notification $notification
    ) {
        $this->ruleFactory = $ruleFactory;
        $this->customerFactory = $customerFactory;
        $this->storeManager = $storeManager;
        $this->transactionFactory = $transactionFactory;
        $this->registry = $registry;
        $this->rewardActionHelper = $rewardActionHelper;
        $this->subscriber = $subscriber;
        $this->pointShowing = $pointShowing;
        $this->rewardPoint = $rewardPoint;
        $this->notification = $notification;
    }

    /**
     * Resolve
     *
     * @param Field $field
     * @param ContextInterface $context
     * @param ResolveInfo $info
     * @param array|null $value
     * @param array|null $args
     * @return array|Value|mixed
     * @throws LocalizedException
     * @throws NoSuchEntityException
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function resolve(Field $field, $context, ResolveInfo $info, array $value = null, array $args = null)
    {
        $customerId = $context->getUserId();
        $customerGroupId = $this->pointShowing->getCustomerGroupId();
        $balance_info = $this->getBalanceInfo($customerId);
        $notify = $this->notification->getNotificationByCustomer($customerId);
        $ratePoint = $this->rewardPoint->getRateCurrencytoPoint();
        $result = [];
        $result['point'] = $balance_info->getPointBalance();
        $result['point_used'] = $balance_info->getPointSpent();
        $result['point_expired'] = $balance_info->getPointExpired();
        $result['amount'] = $balance_info->getAmount();
        $result['notify_balance'] = $notify['notify_balance'];
        $result['notify_expiration'] = $notify['notify_expiration'];
        $result['rate_point'] = $ratePoint;
        $result['subscribe_point'] = $this->getSubscriberPoint($customerGroupId);
        $result['first_review_point'] = $this->getReviewPoint($customerGroupId);
        $result['birth_day_point'] = $this->getBirthDayPoint($customerGroupId);
        return $result;
    }

    /**
     * Get balance info
     *
     * @param int $customerId
     * @return \Bss\RewardPoint\Model\Transaction
     * @throws NoSuchEntityException|LocalizedException
     */
    public function getBalanceInfo($customerId)
    {
        $websiteId = $this->rewardPoint->getWebsiteId();

        return $this->rewardPoint->getTransaction()->loadByCustomer($customerId, $websiteId);
    }

    /**
     * Get Subscribe Point
     *
     * @param int $customerGroupId
     * @return array
     * @throws LocalizedException
     * @throws NoSuchEntityException
     */
    protected function getSubscriberPoint($customerGroupId)
    {
        $result = [];
        $checkSubscriber = $this->subscriber->loadByEmail($this->pointShowing->getCustomer()->getEmail());
        $isLoggin = $this->pointShowing->customerLoggedIn();
        $point = $this->rewardActionHelper->getSubcriberRulePoint($customerGroupId);
        $point = ($isLoggin && $checkSubscriber->isSubscribed()) ? 0 : $point;
        if ($point > 0) {
            $result['point'] = $point;
            $result['message'] = __("You can earn %1 points by subscribe newsletter", $point);
            return $result;
        }
        return $result;
    }

    /**
     * Get birth day point
     *
     * @param $customerGroupId
     * @return mixed
     * @throws NoSuchEntityException
     */
    public function getBirthDayPoint($customerGroupId) {
        $store = $this->storeManager->getStore();
        $websiteId = $store->getWebsite()->getId();
        $points = null;
        if ($customerGroupId === null) {
            $customerGroupId = $this->helper->getValueConfig(
                'customer/create_account/default_group',
                ScopeInterface::SCOPE_STORE
            );
        }
        $object = $this->customerFactory->create();
        $rulesCollection = $this->ruleFactory->create()->getCollection();
        $rulesCollection->addWebsiteGroupDateFilter($websiteId, $customerGroupId);
        $rulesCollection->addFieldToFilter('type', RuleType::RULE_TYE_CUSTOM)->setOrder('priority', 'DESC');

        foreach ($rulesCollection as $rule) {
            $rule->afterLoad();
            $object->setAction(TransactionActions::BIRTHDAY);

            if (!$rule->validate($object)) {
                continue;
            }

            $points = $rule->getPoint();
            return $points;
        }
    }

    /**
     * Get message for review rule
     *
     * @param int $customerGroupId
     * @return \Magento\Framework\Phrase|string
     * @throws NoSuchEntityException
     */
    protected function getReviewPoint($customerGroupId)
    {
        $points = null;
        if ($this->checkFirstAction()) {
            $store = $this->storeManager->getStore();
            $websiteId = $store->getWebsite()->getId();
            if ($customerGroupId === null) {
                $customerGroupId = $this->helper->getValueConfig(
                    'customer/create_account/default_group',
                    ScopeInterface::SCOPE_STORE
                );
            }
            $object = $this->customerFactory->create();
            $rulesCollection = $this->ruleFactory->create()->getCollection();
            $rulesCollection->addWebsiteGroupDateFilter($websiteId, $customerGroupId);
            $rulesCollection->addFieldToFilter('type', RuleType::RULE_TYE_CUSTOM)->setOrder('priority', 'DESC');

            foreach ($rulesCollection as $rule) {
                $rule->afterLoad();
                $object->setAction(TransactionActions::FIRST_REVIEW);

                if (!$rule->validate($object)) {
                    continue;
                }

                $points = $rule->getPoint();
                return $points;
            }
        }
    }


    /**
     * @return bool
     */
    protected function checkFirstAction() {
        $customerId = $this->pointShowing->getCustomerId();
        $transactions = $this->transactionFactory->create()
                        ->addFieldToFilter('customer_id', $customerId)
                        ->addFieldToFilter('action', TransactionActions::REVIEW);
        if ($transactions->getSize() > 0) {
            return false;
        }
        return true;
    }

}
