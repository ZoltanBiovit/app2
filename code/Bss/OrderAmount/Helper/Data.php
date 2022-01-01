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
 * @package    Bss_OrderAmount
 * @author     Extension Team
 * @copyright  Copyright (c) 2015-2019 BSS Commerce Co. ( http://bsscommerce.com )
 * @license    http://bsscommerce.com/Bss-Commerce-License.txt
 */
namespace Bss\OrderAmount\Helper;

/**
 * Class Data
 *
 * @package Bss\OrderAmount\Helper
 */
class Data extends \Magento\Framework\App\Helper\AbstractHelper
{
    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $customerSession;

    /**
     * @var \Magento\Framework\Serialize\SerializerInterface
     */
    protected $serializer;

    /**
     * Data constructor.
     * @param \Magento\Framework\App\Helper\Context $context
     * @param \Magento\Customer\Model\Session $customerSession
     * @param \Magento\Framework\Serialize\SerializerInterface $serializer
     */
    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Framework\Serialize\SerializerInterface $serializer
    ) {
        parent::__construct($context);
        $this->customerSession = $customerSession;
        $this->serializer = $serializer;
    }

    /**
     * @param mixed $store
     * @return bool|mixed
     */
    public function getAmountData($store = null)
    {
        $amountData = $this->scopeConfig->getValue(
            'sales/minimum_order/amount',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
            $store
        );

        try {
            return $this->serializer->unserialize($amountData);
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * @param mixed $groupId
     * @param mixed $store
     * @return bool|float|int
     */
    public function getAmoutDataForCustomerGroup($groupId = null, $store = null)
    {
        $amountData = $this->getAmountData($store);

        if (empty($groupId)) {
            $groupId = $this->customerSession->getCustomerGroupId();
        }

        if ($amountData && is_array($amountData)) {
            $minAmount = 0;
            foreach ($amountData as $value) {
                if ($value['customer_group'] == $groupId) {
                    $minAmount = isset($value['minimum_amount']) ? (float) $value['minimum_amount'] : 0;
                }
            }

            return $minAmount;
        }

        return false;
    }

    /**
     * @param mixed $groupId
     * @param mixed $store
     * @return mixed|string
     */
    public function getMessage($groupId = null, $store = null)
    {
        $amountData = $this->getAmountData($store);

        if (empty($groupId)) {
            $groupId = $this->customerSession->getCustomerGroupId();
        }
        $message = '';
        if ($amountData) {
            foreach ($amountData as $value) {
                if ($value['customer_group'] == $groupId) {
                    $message = isset($value['message']) ? $value['message'] : '';
                }
            }
        }
        return $message;
    }
}
