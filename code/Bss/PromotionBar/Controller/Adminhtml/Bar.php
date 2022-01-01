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

namespace Bss\PromotionBar\Controller\Adminhtml;

/**
 * Class Bar
 * @package Bss\PromotionBar\Controller\Adminhtml
 */
abstract class Bar extends \Magento\Backend\App\Action
{
    /**
     * Bar Factory
     *
     * @var \Bss\PromotionBar\Model\BarFactory
     */
    protected $barFactory;

    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    protected $coreRegistry;

    /**
     * Result redirect factory
     *
     * @var \Magento\Backend\Model\View\Result\RedirectFactory
     */
    protected $resultRedirectFactory;

    /**
     * Constructor
     *
     * @param \Bss\PromotionBar\Model\BarFactory $barFactory
     * @param \Magento\Framework\Registry $coreRegistry
     * @param \Magento\Backend\App\Action\Context $context
     */
    public function __construct(
        \Bss\PromotionBar\Model\BarFactory $barFactory,
        \Magento\Framework\Registry $coreRegistry,
        \Magento\Backend\App\Action\Context $context
    ) {
        $this->barFactory = $barFactory;
        $this->coreRegistry = $coreRegistry;
        $this->resultRedirectFactory = $context->getResultRedirectFactory();
        parent::__construct($context);
    }

    /**
     * Init Promotion Bar
     *
     * @return \Bss\PromotionBar\Model\Bar
     */
    protected function _initBar()
    {
        $barId = (int)$this->getRequest()->getParam('bar_id');
        /** @var \Bss\PromotionBar\Model\Bar $promotionBar */
        $promotionBar = $this->barFactory->create();
        if ($barId) {
            $promotionBar->load($barId);
        }
        $this->coreRegistry->register('bss_promotionbar_bar', $promotionBar);
        return $promotionBar;
    }
}
