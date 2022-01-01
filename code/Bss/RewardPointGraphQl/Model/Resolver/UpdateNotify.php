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
use Magento\Framework\GraphQl\Exception\GraphQlInputException;
use Magento\Framework\GraphQl\Query\Resolver\ContextInterface;
use Magento\Framework\GraphQl\Query\Resolver\Value;
use Magento\Framework\GraphQl\Query\ResolverInterface;
use Magento\Framework\GraphQl\Schema\Type\ResolveInfo;
use \Bss\RewardPoint\Model\NotificationFactory;

/**
 * Class UpdateNotify
 *
 * @package Bss\RewardPointGraphQl\Model\Resolver
 */
class UpdateNotify implements ResolverInterface
{
    /**
     * @var NotificationFactory
     */
    protected $notification;

    /**
     * UpdateNotify constructor.
     *
     * @param NotificationFactory $notification
     */
    public function __construct(
      \Bss\RewardPoint\Model\NotificationFactory $notification
    ) {
        $this->notification = $notification;
    }

    /**
     * @param Field $field
     * @param ContextInterface $context
     * @param ResolveInfo $info
     * @param array|null $value
     * @param array|null $args
     * @return Value|mixed
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function resolve(Field $field, $context, ResolveInfo $info, array $value = null, array $args = null)
    {
        $result = [];
        $customerId = $context->getUserId();
        if (empty($customerId)) {
            throw new GraphQlInputException(__('You can token customer!'));
        }
        $status = [0 , 1];
        $sttBalance = $args['input']['notify_balance'];
        $sttExpiration = $args['input']['notify_expiration'];
        if (!in_array($sttBalance, $status) || !in_array($sttExpiration, $status)) {
            throw new GraphQlInputException(__('Required parameter "notify_balance/notify_expiration" is missing'));
        }
        $notify = $this->notification->create();
        $notify->load($customerId, 'customer_id');
        $notify->setNotifyBalance($sttBalance);
        $notify->setNotifyExpiration($sttExpiration);
        try {
            $notify->save();
            $result['success'] = true;
            $result['message'] = __('Save Successfully!');
        } catch (Exception $e) {
            $result['success'] = false;
            $result['message'] = __('Something went wrong while saving!');
        }

        return ['status' => $result];
    }
}
