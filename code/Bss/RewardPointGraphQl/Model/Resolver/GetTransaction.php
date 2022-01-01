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

use Bss\RewardPoint\Model\ResourceModel\Transaction\CollectionFactory;
use Magento\Framework\GraphQl\Config\Element\Field;
use Magento\Framework\GraphQl\Query\Resolver\ContextInterface;
use Magento\Framework\GraphQl\Query\Resolver\Value;
use Magento\Framework\GraphQl\Query\ResolverInterface;
use Magento\Framework\GraphQl\Schema\Type\ResolveInfo;

/**
 * Class GetTransaction
 *
 * @package Bss\RewardPointGraphQl\Model\Resolver
 */
class GetTransaction implements ResolverInterface
{
    /**
     * @var CollectionFactory
     */
    protected $transactionFactory;

    /**
     * @var \Bss\RewardPoint\Model\Config\Source\TransactionActions
     */
    protected $actions;

    /**
     * GetTransaction constructor.
     * @param \Bss\RewardPoint\Model\Config\Source\TransactionActions $actions
     * @param CollectionFactory $transactionFactory
     */
    public function __construct(
        \Bss\RewardPoint\Model\Config\Source\TransactionActions $actions,
        CollectionFactory $transactionFactory
    ) {
        $this->actions = $actions;
        $this->transactionFactory = $transactionFactory;
    }

    /**
     * Resolve
     *
     * @param Field $field
     * @param ContextInterface $context
     * @param ResolveInfo $info
     * @param array|null $value
     * @param array|null $args
     * @return array[]|Value|mixed
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function resolve(Field $field, $context, ResolveInfo $info, array $value = null, array $args = null)
    {
        $items = [];
        $customerId = $context->getUserId();
        $transactions = $this->transactionFactory->create()->addFieldToFilter('customer_id', $customerId);
        foreach ($transactions as $transaction) {
            $items[] = [
                'transaction_id' => $transaction->getTransactionId(),
                'website_id' => $transaction->getWebsiteId(),
                'customer_id' => $transaction->getCustomerId(),
                'point' => $transaction->getPoint(),
                'point_used' => $transaction->getPointUsed(),
                'point_expired' => $transaction->getPointExpired(),
                'amount' => $transaction->getAmount(),
                'base_currrency_code' => $transaction->getBaseCurrrencyCode(),
                'basecurrency_to_point_rate' => $transaction->getBasecurrencyToPointRate(),
                'action_id' => $transaction->getActionId(),
                'action' => $this->getActionType((int)$transaction->getAction()),
                'created_at' => $transaction->getCreatedAt(),
                'note' => $transaction->getNote(),
                'created_by' => $transaction->getCreatedBy(),
                'is_expired' => $transaction->getIsExpired(),
                'expires_at' => $transaction->getExpiresAt(),
            ];
        }
        return ['items' => $items];
    }

    /**
     * Get action label
     *
     * @param $action
     * @return mixed|null
     */
    public function getActionType($action)
    {
        $result = null;
        $actions = $this->actions->toOptionArray();
        foreach ($actions as $item) {
            if ($action == $item['value']) {
                $result = $item['label'];
            }
        }
        return $result;
    }
}
