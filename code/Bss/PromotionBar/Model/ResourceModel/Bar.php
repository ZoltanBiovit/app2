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
 * @package    Bss_PromotionBar
 * @author     Extension Team
 * @copyright  Copyright (c) 2017-2018 BSS Commerce Co. ( http://bsscommerce.com )
 * @license    http://bsscommerce.com/Bss-Commerce-License.txt
 */

namespace Bss\PromotionBar\Model\ResourceModel;

/**
 * Class Bar
 * @package Bss\PromotionBar\Model\ResourceModel
 */
class Bar extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    /**
     * Date model
     *
     * @var \Magento\Framework\Stdlib\DateTime\TimezoneInterface
     */
    protected $dateTime;

    /**
     * Constructor
     *
     * @param \Magento\Framework\Stdlib\DateTime\TimezoneInterface $date
     * @param \Magento\Framework\Model\ResourceModel\Db\Context $context
     */
    public function __construct(
        \Magento\Framework\Stdlib\DateTime\TimezoneInterface $date,
        \Magento\Framework\Model\ResourceModel\Db\Context $context
    ) {
        $this->dateTime = $date;
        parent::__construct($context);
    }

    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('bss_promotionbar_bar', 'bar_id');
    }

    /**
     * Retrieves Promotion Bar from DB by position
     *
     * @param string $position
     * @return array
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getPromotionBarByPositionAndDate($position)
    {
        $adapter = $this->getConnection();
        $select = $adapter->select()
            ->from($this->getMainTable())
            ->where('position = :position')
            ->where('status = 1');
        $binds = [
            'position' => (int)$position
        ];
        return $adapter->fetchAssoc($select, $binds);
    }

    /**
     * Before save callback
     *
     * @param \Magento\Framework\Model\AbstractModel|\Bss\PromotionBar\Model\Bar $object
     * @return \Magento\Framework\Model\ResourceModel\Db\AbstractDb
     */
    protected function _beforeSave(\Magento\Framework\Model\AbstractModel $object)
    {
        $object->setUpdatedAt($this->dateTime->date());
        if ($object->isObjectNew()) {
            $object->setCreatedAt($this->dateTime->date());
        }
        return parent::_beforeSave($object);
    }
}
