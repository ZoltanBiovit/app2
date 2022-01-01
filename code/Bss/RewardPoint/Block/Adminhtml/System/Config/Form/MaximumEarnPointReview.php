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
namespace Bss\RewardPoint\Block\Adminhtml\System\Config\Form;

use Magento\Config\Block\System\Config\Form\Field\FieldArray\AbstractFieldArray;

/**
 * Class MaximumEarnPointReview
 *
 * @package Bss\RewardPoint\Block\Adminhtml\System\Config\Form
 */
class MaximumEarnPointReview extends AbstractFieldArray
{
    /**
     * Grid columns
     *
     * @var array
     */
    protected $_columns = [];

    /**
     * Add After
     *
     * @var bool
     */
    protected $_addAfter = false;

    /**
     * Add Button Label
     *
     * @var string
     */
    protected $_addButtonLabel;

    /**
     * @var string
     */
    protected $_template = 'Bss_RewardPoint::system/config/form/field/array.phtml';

    /**
     * Prepare to render
     *
     * @return void
     */
    protected function _prepareToRender()
    {
        $this->addColumn('type_date', ['label' => __('')]);
        $this->addColumn('period_time', ['label' => __('Period time')]);
        $this->addColumn('maximum_point_review', ['label' => __('Max Point')]);
    }

    /**
     * @return array
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getArrayRows()
    {
        $result = [];
        /** @var \Magento\Framework\Data\Form\Element\AbstractElement */
        $element = $this->getElement();
        if ($element->getValue() && is_array($element->getValue())) {
            $result = $element->getValue();
        }
        return $result;
    }
}
