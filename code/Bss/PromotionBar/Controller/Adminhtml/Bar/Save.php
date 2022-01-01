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
 * Class Save
 * @package Bss\PromotionBar\Controller\Adminhtml\Bar
 */
class Save extends \Bss\PromotionBar\Controller\Adminhtml\Bar
{
    /**
     * Backend session
     *
     * @var \Magento\Backend\Model\Session
     */
    protected $backendSession;

    /**
     * @var \Magento\Framework\App\Cache\TypeList
     */
    protected $cacheType;

    /**
     * Save constructor.
     * @param \Bss\PromotionBar\Model\BarFactory $barFactory
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Framework\App\Cache\TypeList $cacheType
     * @param \Magento\Backend\App\Action\Context $context
     */
    public function __construct(
        \Bss\PromotionBar\Model\BarFactory $barFactory,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\App\Cache\TypeList $cacheType,
        \Magento\Backend\App\Action\Context $context
    ) {
        $this->backendSession = $context->getSession();
        $this->cacheType = $cacheType;
        parent::__construct($barFactory, $registry, $context);
    }

    /**
     * Execute
     *
     * @return \Magento\Framework\Controller\Result\Redirect
     * @throws \Exception
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     */
    public function execute()
    {
        $data = $this->getRequest()->getPost('bar');
        $data = $this->filterPostData($data);
        $resultRedirect = $this->resultRedirectFactory->create();
        if ($data) {
            $bar = $this->_initBar();
            $startDate = strtotime($data['display_from']);
            $endDate = strtotime($data['display_to']);

            if ($endDate < $startDate && $endDate || ($endDate && !$startDate)) {
                $this->messageManager
                    ->addErrorMessage(__('Make sure the To Date is later than or the same as the From Date.'));
                if (isset($data['bar_id'])) {
                    $resultRedirect->setPath(
                        'bss_promotionbar/*/edit',
                        [
                            'bar_id' => $data['bar_id'],
                            '_current' => true
                        ]
                    );
                } else {
                    $resultRedirect->setPath(
                        'bss_promotionbar/*/new'
                    );
                }
                $this->_getSession()->setBssPromotionBarBarData($data);
                return $resultRedirect;
            }

            $bar->setData($data);
            try {
                $bar->save();
                $this->cacheType->invalidate('full_page');
                $this->messageManager->addSuccessMessage(__('The Promotion Bar has been saved.'));
                $this->backendSession->setBssPromotionBarBarData(false);

                if ($this->getRequest()->getParam('back')) {
                    $resultRedirect->setPath(
                        'bss_promotionbar/*/edit',
                        [
                            'bar_id' => $bar->getId(),
                            '_current' => true
                        ]
                    );
                    return $resultRedirect;
                }

                $resultRedirect->setPath('bss_promotionbar/*/');
                return $resultRedirect;

            } catch (\Magento\Framework\Exception\LocalizedException $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
            } catch (\RuntimeException $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
            } catch (\Exception $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
            }

            $this->_getSession()->setBssPromotionBarBarData($data);
            $resultRedirect->setPath(
                'bss_promotionbar/*/edit',
                [
                    'bar_id' => $bar->getId(),
                    '_current' => true
                ]
            );
            return $resultRedirect;
        }

        $resultRedirect->setPath('bss_promotionbar/*/');
        return $resultRedirect;
    }

    /**
     * Filter Request Post Data
     *
     * @param array $postData
     * @return array
     */
    protected function filterPostData($postData)
    {
        $postData['bar_storeview'] = !empty($postData['bar_storeview']) ? implode(",", $postData['bar_storeview']) : "";
        $postData['customer_group'] = !empty($postData['customer_group']) ?
            implode(",", $postData['customer_group']) : "";
        $postData['page_display'] = !empty($postData['page_display']) ? implode(",", $postData['page_display']) : "";
        return $postData;
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
