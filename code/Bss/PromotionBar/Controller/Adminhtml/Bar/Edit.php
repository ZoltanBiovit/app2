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
 * Class Edit
 * @package Bss\PromotionBar\Controller\Adminhtml\Bar
 */
class Edit extends \Bss\PromotionBar\Controller\Adminhtml\Bar
{
    /**
     * Backend session
     *
     * @var \Magento\Backend\Model\Session
     */
    protected $backendSession;

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
     */
    public function __construct(
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Bss\PromotionBar\Model\BarFactory $barFactory,
        \Magento\Framework\Registry $registry,
        \Magento\Backend\App\Action\Context $context
    ) {
        $this->backendSession = $context->getSession();
        $this->resultPageFactory = $resultPageFactory;
        parent::__construct($barFactory, $registry, $context);
    }

    /**
     * Execute
     *
     * @return \Magento\Backend\Model\View\Result\Page|\Magento\Framework\Controller\Result\Redirect
     */
    public function execute()
    {
        $id = $this->getRequest()->getParam('bar_id');
        /** @var \Bss\PromotionBar\Model\Bar $file */
        $bar = $this->_initBar();
        /** @var \Magento\Backend\Model\View\Result\Page|\Magento\Framework\View\Result\Page $resultPage */
        $resultPage = $this->resultPageFactory->create();
        $resultPage
            ->setActiveMenu('Bss_PromotionBar::promotionbar')
            ->getConfig()->getTitle()->set(__('Promotion Bar'));
        if ($id) {
            $bar->load($id);
            if (!$bar->getId()) {
                $this->messageManager->addErrorMessage(__('This Promotion Bar no longer exists.'));
                $resultRedirect = $this->resultRedirectFactory->create();
                $resultRedirect->setPath(
                    'bss_promotionbar/*/edit',
                    [
                        'bar_id' => $bar->getId(),
                        '_current' => true
                    ]
                );
                return $resultRedirect;
            }
        }

        $title = $bar->getId() ?
            __("%1", $bar->getBarName()) :
            __('New Promotion Bar');
        $resultPage->getConfig()->getTitle()->prepend($title);
        $data = $this->backendSession->getData('bss_promotionbar_bar_data', true);

        if (!empty($data)) {
            $bar->setData($data);
        }
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
