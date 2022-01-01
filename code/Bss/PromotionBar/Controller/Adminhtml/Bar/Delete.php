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

use Braintree\Exception;

/**
 * Class Delete
 * @package Bss\PromotionBar\Controller\Adminhtml\Bar
 */
class Delete extends \Bss\PromotionBar\Controller\Adminhtml\Bar
{

    /**
     * Execute action
     *
     * @return \Magento\Backend\Model\View\Result\Redirect
     * @throws \Exception
     */
    public function execute()
    {
        $resultRedirect = $this->resultRedirectFactory->create();
        $id = $this->getRequest()->getParam('bar_id');
        if ($id) {
            $title = "";
            try {
                /** @var \Bss\PromotionBar\Model\Bar $promotionBar */
                $promotionBar = $this->barFactory->create()->load($id);

                $title = $promotionBar->getBarName();

                $promotionBar->delete();
                $this->messageManager->addSuccessMessage(__('The Promotion Bar has been deleted.'));
                $this->_eventManager->dispatch(
                    'adminhtml_bss_promotionbar_bar_on_delete',
                    ['title' => $title, 'status' => 'success']
                );
                $resultRedirect->setPath('bss_promotionbar/*/');
                return $resultRedirect;
            } catch (\Exception $e) {
                $this->_eventManager->dispatch(
                    'adminhtml_bss_promotionbar_file_on_delete',
                    ['title' => $title, 'status' => 'fail']
                );

                $this->messageManager->addErrorMessage($e->getMessage());

                $resultRedirect->setPath('bss_promotionbar/*/edit', ['bar_id' => $id]);
                return $resultRedirect;
            }
        }

        $this->messageManager->addErrorMessage(__('Promotion Bar to delete was not found.'));
        
        $resultRedirect->setPath('bss_promotionbar/*/');
        return $resultRedirect;
    }

    /**
     * Check Rule
     *
     * @return bool
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed("Bss_PromotionBar::delete");
    }
}
