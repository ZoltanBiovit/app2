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
namespace Bss\PromotionBar\Block\Adminhtml\Bar\Edit;

/**
 * Class Tabs
 * @package Bss\PromotionBar\Block\Adminhtml\Bar\Edit
 */
class Tabs extends \Magento\Backend\Block\Widget\Tabs
{
    /**
     * Construct
     *
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setId('bar_tabs');
        $this->setDestElementId('edit_form');
        $this->setTitle(__('Promotion Bar Information'));
    }
}
