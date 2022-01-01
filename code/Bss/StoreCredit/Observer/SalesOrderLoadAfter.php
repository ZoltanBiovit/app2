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

/**
 * Class SalesOrderLoadAfter
 * @package Bss\StoreCredit\Observer
 */
class SalesOrderLoadAfter implements ObserverInterface
{
    /**
     * @var \Magento\Sales\Api\Data\OrderExtension
     */
    private $orderExtension;

    /**
     * @var \Bss\StoreCredit\Model\ResourceModel\History\Collection
     */
    private $historyCollection;

    /**
     * SalesOrderLoadAfter constructor.
     * @param \Magento\Sales\Api\Data\OrderExtension $orderExtension
     * @param \Bss\StoreCredit\Model\ResourceModel\History\Collection $historyCollection
     */
    public function __construct(
        \Magento\Sales\Api\Data\OrderExtension $orderExtension,
        \Bss\StoreCredit\Model\ResourceModel\History\Collection $historyCollection
    ) {
        $this->orderExtension = $orderExtension;
        $this->historyCollection = $historyCollection;
    }

    /**
     * @param \Magento\Framework\Event\Observer $observer
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $order = $observer->getOrder();
        $extensionAttributes = $order->getExtensionAttributes();
        if ($extensionAttributes === null) {
            $extensionAttributes = $this->orderExtension;
        }

        $storeCreditData = $this->getStoreCreditHistoryRecord($order->getEntityId());
        if (!$storeCreditData->isEmpty()) {
            $extensionAttributes->setBssStoreCredit(
                $this->getStoreCreditHistoryRecord($order->getEntityId())
            );
            $order->setExtensionAttributes($extensionAttributes);
        }
    }

    /**
     * @param int $orderId
     * @return \Magento\Framework\DataObject
     */
    protected function getStoreCreditHistoryRecord($orderId)
    {
        return $this->historyCollection
            ->addFieldToFilter('order_id', $orderId)
            ->getLastItem();
    }
}
