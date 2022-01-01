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
namespace Bss\PromotionBar\Model;

/**
 * Class Bar
 * @package Bss\PromotionBar\Model
 */
class Bar extends \Magento\Framework\Model\AbstractModel
{
    /**
     * Cache tag constant
     *
     * @var string
     */
    const CACHE_TAG = 'bss_promotionbar_bar';

    /**
     * Cache tag
     *
     * @var string
     */
    protected $_cacheTag = 'bss_promotionbar_bar';

    /**
     * Event prefix
     *
     * @var string
     */
    protected $_eventPrefix = 'bss_promotionbar_bar';

    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(\Bss\PromotionBar\Model\ResourceModel\Bar::class);
    }

    /**
     * Get identities
     *
     * @return array
     */
    public function getIdentities()
    {
        return [self::CACHE_TAG . '_' . $this->getId()];
    }

    /**
     * Get entity default values
     *
     * @return array
     */
    public function getDefaultValues()
    {
        $values = [];
        return $values;
    }
}
