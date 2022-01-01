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

use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\GraphQl\Config\Element\Field;
use Magento\Framework\GraphQl\Exception\GraphQlInputException;
use Magento\Framework\GraphQl\Query\Resolver\ContextInterface;
use Magento\Framework\GraphQl\Query\Resolver\Value;
use Magento\Framework\GraphQl\Query\ResolverInterface;
use Magento\Framework\GraphQl\Schema\Type\ResolveInfo;

/**
 * Class ApplyRewardPoint
 *
 * @package Bss\RewardPointGraphQl\Model\Resolver
 */
class ApplyRewardPoint implements ResolverInterface
{
    /**
     * @var \Bss\RewardPoint\Model\RewardPointManagement
     */
    protected $rewardPointManagement;

    /**
     * @var \Magento\Framework\Json\Helper\Data
     */
    protected $jsonHelper;

    /**
     * @var \Magento\Quote\Model\QuoteFactory
     */
    protected $quoteFactory;

    /**
     * @var \Magento\Quote\Model\MaskedQuoteIdToQuoteIdInterface
     */
    protected $maskedQuoteIdToQuoteId;

    /**
     * ApplyRewardPoint constructor.
     * @param \Magento\Quote\Model\QuoteFactory $quoteFactory
     * @param \Bss\RewardPoint\Model\RewardPointManagement $rewardPointManagement
     * @param \Magento\Framework\Json\Helper\Data $jsonHelper
     * @param \Magento\Quote\Model\MaskedQuoteIdToQuoteIdInterface $maskedQuoteIdToQuoteId
     */
    public function __construct(
        \Magento\Quote\Model\QuoteFactory $quoteFactory,
        \Bss\RewardPoint\Model\RewardPointManagement $rewardPointManagement,
        \Magento\Framework\Json\Helper\Data $jsonHelper,
        \Magento\Quote\Model\MaskedQuoteIdToQuoteIdInterface $maskedQuoteIdToQuoteId
    ) {
        $this->rewardPointManagement = $rewardPointManagement;
        $this->jsonHelper = $jsonHelper;
        $this->quoteFactory = $quoteFactory;
        $this->maskedQuoteIdToQuoteId = $maskedQuoteIdToQuoteId;
    }

    /**
     * Resolver
     *
     * @param Field $field
     * @param ContextInterface $context
     * @param ResolveInfo $info
     * @param array|null $value
     * @param array|null $args
     * @return array[]|Value|mixed
     * @throws GraphQlInputException
     * @throws LocalizedException
     * @throws NoSuchEntityException
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function resolve(Field $field, $context, ResolveInfo $info, array $value = null, array $args = null)
    {
        $result = [];
        $customer = $context->getUserId();
        if (empty($args['input']['cart_id'])) {
            throw new GraphQlInputException(__('Required parameter "cart_id" is missing'));
        }
        $hashCart = $args['input']['cart_id'];
        $quoteId= $this->maskedQuoteIdToQuoteId->execute($hashCart);

        if (!isset($args['input']['amount'])) {
            throw new GraphQlInputException(__('Required parameter "amount" is missing'));
        }
        $amount = $args['input']['amount'];
        $storeId = (int)$context->getExtensionAttributes()->getStore()->getId();
        $quote = $this->quoteFactory->create()->load($quoteId);
        if ($quote->getCustomer()->getId() == $customer && $quote->getStore()->getId() == $storeId) {
            $response = $this->rewardPointManagement->apply($amount, $quote);
            if ($response) {
                $responses = $this->jsonHelper->jsonDecode($response);
                if ($responses['status_message'] == 'success') {
                    $result['success'] = true;
                    $result['error_message'] = __('Successfully');
                } else {
                    $result['success'] = false;
                    $result['error_message'] = $responses['message'];
                }
            }
        } else {
            $result['success'] = false;
            $result['error_message'] = __('The current user cannot perform operations on cart "cart_id"');
        }
        return ['cart' => $result];
    }
}
