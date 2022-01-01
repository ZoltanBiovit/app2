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

namespace Bss\PromotionBar\Block\Adminhtml\Bar\Edit\Tab;

use Magento\Backend\Block\Widget\Tab\TabInterface;

/**
 * Class Display
 * @package Bss\PromotionBar\Block\Adminhtml\Bar\Edit\Tab
 */
class Display extends \Magento\Backend\Block\Widget\Form\Generic implements TabInterface
{
    /**
     * Yes no options
     *
     * @var \Magento\Config\Model\Config\Source\Yesno
     */
    protected $booleanOptions;

    /**
     * Page Display options
     *
     * @var \Bss\PromotionBar\Model\Source\PageDisplay
     */
    protected $pageDisplay;

    /**
     * Position options
     *
     * @var \Bss\PromotionBar\Model\Source\Position
     */
    protected $positionOption;

    /**
     * Constructor
     *
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Framework\Data\FormFactory $formFactory
     * @param \Bss\PromotionBar\Model\Source\PageDisplay $pageDisplay
     * @param \Bss\PromotionBar\Model\Source\Position $position
     * @param \Magento\Config\Model\Config\Source\Yesno $booleanOptions
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Data\FormFactory $formFactory,
        \Bss\PromotionBar\Model\Source\PageDisplay $pageDisplay,
        \Bss\PromotionBar\Model\Source\Position $position,
        \Magento\Config\Model\Config\Source\Yesno $booleanOptions,
        $data = []
    ) {
        $this->booleanOptions = $booleanOptions;
        $this->pageDisplay = $pageDisplay;
        $this->positionOption = $position;
        parent::__construct($context, $registry, $formFactory, $data);
    }

    /**
     * Prepare Form
     *
     * @return \Magento\Backend\Block\Widget\Form\Generic
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function _prepareForm()
    {
        /** @var \Bss\PromotionBar\Model\Bar $promotionBar */
        $promotionBar = $this->_coreRegistry->registry('bss_promotionbar_bar');
        $form = $this->_formFactory->create();
        $form->setHtmlIdPrefix('bar_');
        $form->setFieldNameSuffix('bar');

        $fieldset = $form->addFieldset(
            'base_fieldset',
            [
                'legend' => __('Display Setting'),
                'class' => 'fieldset-wide'
            ]
        );

        $fieldset->addField(
            'page_display',
            'multiselect',
            [
                'name' => 'page_display',
                'label' => __('Display on Page'),
                'title' => __('Display on Page'),
                'style' => 'height:15em;',
                'values' => $this->pageDisplay->toOptionArray(),
                'required' => true,
            ]
        );

        $fieldset->addField(
            'exclude_category',
            'text',
            [
                'name' => 'exclude_category',
                'label' => __('Exclude Categories'),
                'title' => __('Exclude Categories'),
                'note' => __(
                    "Enter ID of Categories to be excluded from the Display Rule. Example: 3,4,5.
                    All the product pages belong to the category will be excluded promotion bar."
                )
            ]
        );

        $fieldset->addField(
            'exclude_product',
            'text',
            [
                'name' => 'exclude_product',
                'label' => __('Exclude Products'),
                'title' => __('Exclude Products'),
                'note' => __("Enter ID of Products to be excluded from the Display Rule. Example: 3,4,5")
            ]
        );

        $fieldset->addField(
            'position',
            'select',
            [
                'name' => 'position',
                'label' => __('Position'),
                'title' => __('Position'),
                'values' => $this->positionOption->toOptionArray(),
            ]
        );

        $fieldset->addField(
            'hide_after',
            'text',
            [
                'name' => 'hide_after',
                'label' => __('Auto Close Promotion Bar After (seconds)'),
                'title' => __('Auto Close Promotion Bar After (seconds)'),
                'class' => 'validate-zero-or-greater',
                'note' => __("Fill “0” to disable auto close of the Promotion Bar.")
            ]
        );

        $barData = $this->_session->getData('bss_promotionbar_bar_data', true);
        if ($barData) {
            $promotionBar->addData($barData);
        } else {
            if (!$promotionBar->getId()) {
                $promotionBar->addData($promotionBar->getDefaultValues());
            }
        }

        $form->addValues($promotionBar->getData());
        $this->setForm($form);
        return parent::_prepareForm();
    }

    /**
     * Prepare label for tab
     *
     * @return string
     */
    public function getTabLabel()
    {
        return __('Display Rule');
    }

    /**
     * Prepare title for tab
     *
     * @return string
     */
    public function getTabTitle()
    {
        return $this->getTabLabel();
    }

    /**
     * Can show tab in tabs
     *
     * @return boolean
     */
    public function canShowTab()
    {
        return true;
    }

    /**
     * Tab is hidden
     *
     * @return boolean
     */
    public function isHidden()
    {
        return false;
    }
}
