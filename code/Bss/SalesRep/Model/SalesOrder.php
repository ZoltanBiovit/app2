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
 * @category  BSS
 * @package   Bss_SalesRep
 * @author    Extension Team
 * @copyright Copyright (c) 2020 BSS Commerce Co. ( http://bsscommerce.com )
 * @license   http://bsscommerce.com/Bss-Commerce-License.txt
 */
namespace Bss\SalesRep\Model;

use Magento\Framework\Model\AbstractModel;

/**
 * Class SalesOrder
 *
 * @package Bss\SalesRep\Model
 */
class SalesOrder extends AbstractModel
{
    /**
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Bss\SalesRep\Model\ResourceModel\SalesOrder');
    }
}