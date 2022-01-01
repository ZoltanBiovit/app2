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
namespace Bss\RewardPoint\Model;

use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Model\AbstractModel;

/**
 * Class Transaction
 *
 * @package Bss\RewardPoint\Model
 */
class Transaction extends AbstractModel
{

    protected $rewardHelper;

    /**
     * Transaction constructor.
     *
     * @param \Magento\Framework\Model\Context $context
     * @param \Bss\RewardPoint\Helper\Data $rewardHelper
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Framework\Model\ResourceModel\AbstractResource|null $resource
     * @param \Magento\Framework\Data\Collection\AbstractDb|null $resourceCollection
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Bss\RewardPoint\Helper\Data $rewardHelper,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Model\ResourceModel\AbstractResource $resource = null,
        \Magento\Framework\Data\Collection\AbstractDb $resourceCollection = null,
        array $data = []
    ) {
        $this->rewardHelper = $rewardHelper;
        parent::__construct(
            $context,
            $registry,
            $resource,
            $resourceCollection,
            $data
        );
    }

    /**
     * Model construct that should be used for object initialization
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(\Bss\RewardPoint\Model\ResourceModel\Transaction::class);
    }

    /**
     * @param int $customerId
     * @param int $websiteId
     * @return $this
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function loadByCustomer($customerId, $websiteId)
    {
        $data = $this->_getResource()->loadByCustomer($customerId, $websiteId);
        return $this->setData($data);
    }

    /**
     * @param array $bind
     * @return mixed
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getPointBalanceReview($bind)
    {
        return $this->_getResource()->getPointBalanceReview($bind);
    }

    /**
     * @return mixed
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function updatePointUsed()
    {
        return $this->_getResource()->updatePointUsed($this);
    }

    /**
     * @return mixed
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function updatePointExpired()
    {
        return $this->_getResource()->updatePointExpired();
    }

    /**
     * @return bool|false|float|int|string|null
     */
    public function getExpiresAt()
    {
        $expriesAt = (int)$this->getData('expires_at');
        $createdAt = $this->getData('created_at');
        return $this->rewardHelper->convertExpiredDayToDate($expriesAt, $createdAt);
    }

    /**
     * @param int|null $expiredAt
     * @return $this
     * @throws LocalizedException
     */
    public function setExpiresAt($expiredAt)
    {
        if ($expiredAt && is_numeric($expiredAt)) {
            $this->setData('expires_at', $expiredAt);
            return $this;
        }
        throw new LocalizedException(__('expires_at must be number type.'));
    }
}
