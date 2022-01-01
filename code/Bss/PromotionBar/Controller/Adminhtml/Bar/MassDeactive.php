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
 * Class MassDeactive
 * @package Bss\PromotionBar\Controller\Adminhtml\Bar
 */
class MassDeactive extends \Magento\Backend\App\Action
{
    /**
     * Mass Action Filter
     *
     * @var \Magento\Ui\Component\MassAction\Filter
     */
    protected $filter;

    /**
     * Collection Factory
     *
     * @var \Bss\PromotionBar\Model\ResourceModel\Bar\CollectionFactory
     */
    protected $collectionFactory;

    /**
     * Constructor
     *
     * @param \Magento\Ui\Component\MassAction\Filter $filter
     * @param \Bss\PromotionBar\Model\ResourceModel\Bar\CollectionFactory $collectionFactory
     * @param \Magento\Backend\App\Action\Context $context
     */
    public function __construct(
        \Magento\Ui\Component\MassAction\Filter $filter,
        \Bss\PromotionBar\Model\ResourceModel\Bar\CollectionFactory $collectionFactory,
        \Magento\Backend\App\Action\Context $context
    ) {
        $this->filter = $filter;
        $this->collectionFactory = $collectionFactory;
        parent::__construct($context);
    }

    /**
     * Execute action
     *
     * @return \Magento\Backend\Model\View\Result\Redirect|\Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\ResultInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function execute()
    {
        $collection = $this->filter->getCollection($this->collectionFactory->create());
        $deactive = 0;
        foreach ($collection as $item) {
            /** @var \Bss\PromotionBar\Model\Bar $item */
            $this->activePromotionBar($item);
            $deactive++;
        }

        $this->messageManager->addSuccessMessage(__('A total of %1 record(s) have been deactived.', $deactive));
        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultFactory->create(\Magento\Framework\Controller\ResultFactory::TYPE_REDIRECT);
        return $resultRedirect->setPath('*/*/');
    }

    /**
     * Remove promotionBar
     *
     * @param \Bss\PromotionBar\Model\Bar $promotionBar
     * @return \Magento\Backend\Model\View\Result\Redirect|\Magento\Framework\Controller\ResultInterface|void
     * @throws \Exception
     */
    protected function activePromotionBar($promotionBar)
    {
        try {
            $promotionBar->setStatus(0);
            $promotionBar->save();
        } catch (\Exception $e) {
            $this->messageManager->addErrorMessage($e->getMessage());
            $resultRedirect = $this->resultFactory->create(\Magento\Framework\Controller\ResultFactory::TYPE_REDIRECT);
            $resultRedirect->setPath('bss_promotionbar/*/');
            return $resultRedirect;
        }
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
