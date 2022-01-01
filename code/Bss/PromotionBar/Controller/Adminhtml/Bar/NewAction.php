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

namespace Bss\PromotionBar\Controller\Adminhtml\Bar;

/**
 * Class NewAction
 * @package Bss\PromotionBar\Controller\Adminhtml\Bar
 */
class NewAction extends \Bss\PromotionBar\Controller\Adminhtml\Bar
{
    /**
     * Page factory
     *
     * @var \Magento\Framework\View\Result\PageFactory
     */
    protected $resultPageFactory;

    /**
     * Constructor
     *
     * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
     * @param \Bss\PromotionBar\Model\BarFactory $barFactory
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Backend\App\Action\Context $context
     * @internal param \Bss\PromotionBar\Model\BarFactory $BarFactory
     */
    public function __construct(
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Bss\PromotionBar\Model\BarFactory $barFactory,
        \Magento\Framework\Registry $registry,
        \Magento\Backend\App\Action\Context $context
    ) {
        $this->resultPageFactory = $resultPageFactory;
        parent::__construct($barFactory, $registry, $context);
    }

    /**
     * Forward to edit
     *
     * @return \Magento\Framework\View\Result\Page
     */
    public function execute()
    {
        $this->_initBar();

        /** @var \Magento\Backend\Model\View\Result\Page|\Magento\Framework\View\Result\Page $resultPage */
        $resultPage = $this->resultPageFactory->create();
        $resultPage
            ->setActiveMenu('Bss_PromotionBar::promotionbar')
            ->getConfig()->getTitle()->set(__('Promotion Bar'));

        $title = __('New Promotion Bar');
        $resultPage->getConfig()->getTitle()->prepend($title);

        return $resultPage;
    }

    /**
     * Check Rule
     *
     * @return bool
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed("Bss_PromotionBar::save");
    }
}
