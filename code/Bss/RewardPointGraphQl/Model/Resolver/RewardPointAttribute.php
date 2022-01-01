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

use Bss\RewardPoint\Block\Customer\PointShowing;
use Bss\RewardPoint\Helper\RewardCustomAction;
use Magento\Catalog\Model\ProductRepository;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\GraphQl\Config\Element\Field;
use Magento\Framework\GraphQl\Query\Resolver\ContextInterface;
use Magento\Framework\GraphQl\Query\Resolver\Value;
use Magento\Framework\GraphQl\Query\ResolverInterface;
use Magento\Framework\GraphQl\Schema\Type\ResolveInfo;

/**
 * Class RewardPointAttribute
 *
 * @package Bss\RewardPointGraphQl\Model\Resolver
 */
class RewardPointAttribute implements ResolverInterface
{
    /**
     * @var ProductRepository
     */
    protected $product;

    /**
     * @var PointShowing
     */
    protected $message;

    /**
     * @var \Bss\RewardPoint\Helper\RewardCustomAction
     */
    protected $rewardCustomAction;

    /**
     * @var RewardCustomAction
     */
    protected  $rewardActionHelper;

    /**
     * RewardPointAttribute constructor.
     * @param RewardCustomAction $rewardActionHelper
     * @param ProductRepository $product
     * @param PointShowing $message
     * @param \Bss\RewardPoint\Helper\RewardCustomAction $rewardCustomAction
     */
    public function __construct(
        RewardCustomAction $rewardActionHelper,
        ProductRepository $product,
        PointShowing $message,
        \Bss\RewardPoint\Helper\RewardCustomAction $rewardCustomAction
    ) {
        $this->rewardActionHelper = $rewardActionHelper;
        $this->product = $product;
        $this->message = $message;
        $this->rewardCustomAction = $rewardCustomAction;
    }

    /**
     * Reward Point Attribute
     *
     * @param Field $field
     * @param ContextInterface $context
     * @param ResolveInfo $info
     * @param array|null $value
     * @param array|null $args
     * @return array|Value|mixed
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     * @throws NoSuchEntityException|\Magento\Framework\Exception\LocalizedException
     */
    public function resolve(Field $field, $context, ResolveInfo $info, array $value = null, array $args = null)
    {
        $result = [];
        $productId = $value['entity_id'];
        $product = $this->product->getById($productId);
        $point = $this->rewardActionHelper->getPointByProduct($product, $this->message->getCustomerGroupId());
        $message = '';
        if ($point) {
            $message = $this->message->getProductPointMessage($product, $point);
        }
        $result['product_point']['assign_by'] = $product->getData('assign_by');
        $result['product_point']['receive_point'] = $product->getData('receive_point');
        $result['product_point']['dependent_qty'] = $product->getData('dependent_qty');
        $result['product_point']['point'] = $point;
        $result['product_point']['message'] = $message;
        $reviewPoint = (int)$this->rewardCustomAction
                        ->getReviewRulePoint($this->message->getCustomerGroupId(), $product);
        $result['customer_point']['review_point'] = $reviewPoint;
        $result['customer_point']['message'] = $this->getReviewPoint($reviewPoint);
        return $result;
    }

    /**
     * Get review point message
     *
     * @param int $point
     * @return \Magento\Framework\Phrase|string
     */
    protected function getReviewPoint($point)
    {
        if ($point > 0) {
            if ($this->message->customerLoggedIn()) {
                return __("Earn %1 points for each review", $point);
            }
            return __("Login and earn %1 points for each review", $point);
        }
        return '';
    }
}
