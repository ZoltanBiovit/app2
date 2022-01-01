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

namespace Bss\PromotionBar\Block\Adminhtml\Bar;

/**
 * Class Edit
 * @package Bss\PromotionBar\Block\Adminhtml\Bar
 */
class Edit extends \Magento\Backend\Block\Widget\Form\Container
{
    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    protected $coreRegistry;

    /**
     * Constructor
     *
     * @param \Magento\Framework\Registry $coreRegistry
     * @param \Magento\Backend\Block\Widget\Context $context
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\Registry $coreRegistry,
        \Magento\Backend\Block\Widget\Context $context,
        $data = []
    ) {
        $this->coreRegistry = $coreRegistry;
        parent::__construct($context, $data);
    }

    /**
     * Initialize Promotion Bar edit block
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_objectId = 'bar_id';
        $this->_blockGroup = 'Bss_PromotionBar';
        $this->_controller = 'adminhtml_bar';
        parent::_construct();
        $this->buttonList->update('delete', 'label', __('Delete'));
        $this->buttonList->add(
            'save-and-continue',
            [
                'label' => __('Save and Continue Edit'),
                'class' => 'save',
                'data_attribute' => [
                    'mage-init' => [
                        'button' => [
                            'event' => 'saveAndContinueEdit',
                            'target' => '#edit_form'
                        ]
                    ]
                ]
            ],
            -100
        );
        $this->buttonList->update('save', 'label', __('Save Promotion Bar'));
    }

    /**
     * Retrieve text for header element depending on loaded Promotion Bar
     *
     * @return string
     */
    public function getHeaderText()
    {
        /** @var \Bss\PromotionBar\Model\Bar $bar */
        $bar = $this->coreRegistry->registry('bss_promotionbar_bar');
        if ($bar->getId()) {
            return $bar->getBarName();
        }
        return __('New Promotion Bar');
    }
}
