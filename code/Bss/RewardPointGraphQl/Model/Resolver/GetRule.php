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

use Magento\Framework\GraphQl\Config\Element\Field;
use Magento\Framework\GraphQl\Query\Resolver\ContextInterface;
use Magento\Framework\GraphQl\Query\Resolver\Value;
use Magento\Framework\GraphQl\Query\ResolverInterface;
use Magento\Framework\GraphQl\Schema\Type\ResolveInfo;

/**
 * Class GetRule
 *
 * @package Bss\RewardPointGraphQl\Model\Resolver
 */
class GetRule implements ResolverInterface
{
    /**
     * @var \Bss\RewardPoint\Model\ResourceModel\Rule\CollectionFactory
     */
    protected $collectionFactory;

    /**
     * GetRule constructor.
     *
     * @param \Bss\RewardPoint\Model\ResourceModel\Rule\CollectionFactory $collectionFactory
     */
    public function __construct(
        \Bss\RewardPoint\Model\ResourceModel\Rule\CollectionFactory $collectionFactory
    ) {
        $this->collectionFactory = $collectionFactory;
    }

    /**
     * @param Field $field
     * @param ContextInterface $context
     * @param ResolveInfo $info
     * @param array|null $value
     * @param array|null $args
     * @return array[]|Value|mixed
     */
    public function resolve(Field $field, $context, ResolveInfo $info, array $value = null, array $args = null)
    {
        $result = [];
        $rules = $this->collectionFactory->create()->addFieldToFilter('is_active', 1);
        foreach ($rules as $rule) {
            $result[] = [
                'rule_id' => $rule->getRuleId(),
                'name' => $rule->getName(),
                'type' => $rule->getType(),
                'from_date' => $rule->getFromDate(),
                'to_date' => $rule->getToDate(),
                'conditions_serialized' => $rule->getConditionsSerialized(),
                'actions_serialized' => $rule->getActionsSerialized(),
                'product_ids' => $rule->getProductIds(),
                'priority' => $rule->getPriority(),
                'simple_action' => $rule->getSimpleAction(),
                'point' => $rule->getPoint(),
                'purchase_point' => $rule->getPurchasePoint(),
                'spent_amount' => $rule->getSpentAmount()
            ];
        }
        return ['rules' => $result];
    }
}
