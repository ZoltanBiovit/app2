<?php

namespace Bss\CustomQuoteExtension\Controller\Quote;


use Bss\QuoteExtension\Controller\Quote\View as QuoteView;

class View extends QuoteView {
    /**
     * Quote View Page
     *
     * @return Redirect|ResultInterface|Page
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function execute()
    {
        $enable = $this->helper->isEnable();
        $quoteId = $this->getRequest()->getParam('quote_id');
        $quote = $this->quoteExtensionCollection->create()
            ->addFieldToFilter('main_table.entity_id', $quoteId)->getLastItem();
        $subUserId = $quote->getSubUserId();
        $this->coreRegistry->register('sub_user_id_quote', $subUserId);
        $mageQuote = $this->mageQuoteFactory->create()->load($quote->getQuoteId());





//dejan

if($quote->getStatus() == 'updated'){

        // $allItemsFront = $mageQuote->getAllItems();
        // $allItemsFrontArray = [];
        // foreach ($allItemsFront as $item) {
        //     $productId = $item->getProductId();
        //     $allItemsFrontArray[] = $productId;
        // }

        // $backendQuoteId = $quote->getBackendQuoteId();
        // $mageQuote2 = $this->mageQuoteFactory->create()->load($backendQuoteId);
        // $allItemsBackend = $mageQuote2->getAllItems();

        // foreach ($allItemsBackend as $item) {
        //    $productId = $item->getProductId();
        //    if (in_array($productId, $allItemsFrontArray)) {
        //     continue;
        // }
        //     $productQty = $item->getQty();
        //     $productPrice = $item->getPrice();

        //     $product = $this->_productRepository->getById($productId);
        //     $product->setPrice($productPrice);
            
        //     $mageQuote->addProduct($product, intval($productQty))->save();
        // }
    }
//dejan


// $writer = new \Zend\Log\Writer\Stream(BP . '/var/log/11.log');
// $logger = new \Zend\Log\Logger();
// $logger->addWriter($writer);
// $logger->info(print_r('view cisto',true));


        if (!$this->checkPermissionSubUser($subUserId) || !$this->checkCustomerViewQuote($quote->getCustomerId())) {
            $resultRedirect = $this->resultRedirectFactory->create();
            $this->messageManager->addErrorMessage(__('You have no permission to see that quote.'));
            return $resultRedirect->setPath('quoteextension/quote/history');
        }



        if ($enable && $quoteId && $quote->getEntityId() && $mageQuote->getId()) {
            $resultPage = $this->resultPageFactory->create();
            $this->coreRegistry->register('current_quote_extension', $quote);
            $this->coreRegistry->register('current_quote', $mageQuote);
            $resultPage->getConfig()->getTitle()->set(__('Quote # %1', $quote->getIncrementId()));

            /** @var Links $navigationBlock */
            $navigationBlock = $resultPage->getLayout()->getBlock('customer_account_navigation');
            if ($navigationBlock) {
                $navigationBlock->setActive('quoteextension/quote/history');
            }

            $block = $resultPage->getLayout()->getBlock('customer.account.link.back');
            if ($block) {
                $block->setRefererUrl($this->_redirect->getRefererUrl());
            }
            $this->checkoutSession->setIsQuoteExtension($mageQuote->getId());
            return $resultPage;
        } else {
            $resultRedirect = $this->resultRedirectFactory->create();
            $this->messageManager->addErrorMessage(__('The request quote id no longer exists.'));
            return $resultRedirect->setPath('quoteextension/quote/history');
        }
    }
}