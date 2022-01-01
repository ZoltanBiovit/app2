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
 * @package    Bss_CustomQuoteExtension
 * @author     Extension Team
 * @copyright  Copyright (c) 2021 BSS Commerce Co. ( http://bsscommerce.com )
 * @license    http://bsscommerce.com/Bss-Commerce-License.txt
 */
namespace Bss\CustomQuoteExtension\Controller\Quote;

use Bss\QuoteExtension\Model\Config\Source\Status;
use Magento\Framework\Controller\Result\Redirect;


use Magento\Framework\App\Action\Action;

use Bss\QuoteExtension\Helper\Data;
use Bss\QuoteExtension\Helper\Json as JsonHelper;
use Bss\QuoteExtension\Helper\QuoteExtension\Address;

use Bss\QuoteExtension\Model\ManageQuote;
use Bss\QuoteExtension\Model\QuoteItemFactory;
use Bss\QuoteExtension\Model\QuoteVersion;

use Magento\Framework\App\Action\Context;

use Magento\Framework\Data\Form\FormKey\Validator;
use Magento\Quote\Api\CartRepositoryInterface;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Catalog\Model\ProductFactory;



/**
 * Class ViewSubmit
 *
 * @package Bss\QuoteExtension\Controller\Quote
 * @SuppressWarnings(PHPMD.AllPurposeAction)
 */
class ViewSubmit extends \Bss\QuoteExtension\Controller\Quote\ViewSubmit
{

    protected $formKey;   
    protected $cart;
    protected $product;

    /**
     * @var Session
     */
    protected $checkoutSession;

        /**
     * @var ProductFactory
     */
    protected $productFactory;


     public function __construct(
        \Magento\Framework\App\Action\Context $context,
        Validator $formKeyValidator,
        CartRepositoryInterface $quoteRepository,
        ManageQuote $manageQuote,
        QuoteItemFactory $quoteItemFactory,
        Data $helper,
        \Bss\QuoteExtension\Helper\Mail $mailHelper,
        QuoteVersion $quoteVersion,
        JsonHelper $jsonHelper,
        Address $helperQuoteAddress,
        \Magento\Framework\Data\Form\FormKey $formKey,
        \Magento\Checkout\Model\Cart $cart,
        \Magento\Catalog\Model\Product $product,
        \Magento\Checkout\Model\Session $checkoutSession,
        \Magento\Customer\Model\Session $customerSession,
        JsonFactory $resultJsonFactory,
        ProductFactory $productFactory
        ){
            $this->formKey = $formKey;
            $this->cart = $cart;
            $this->product = $product;  
            $this->checkoutSession = $checkoutSession;
            $this->customerSession = $customerSession;
            $this->resultJsonFactory = $resultJsonFactory;
            $this->productFactory = $productFactory;
            parent::__construct($context,$formKeyValidator, $quoteRepository, $manageQuote, $quoteItemFactory, $helper, $mailHelper, $quoteVersion, $jsonHelper, $helperQuoteAddress);
        }

    /**
     * Execute Function
     */
    public function execute()
    {
        if (!$this->formKeyValidator->validate($this->getRequest())) {
            return $this->resultRedirectFactory->create()->setPath('*/*/history');
        }

        $params = $this->getRequest()->getParams();
        if (isset($params['request_entity_id'])) {
            try {
                $manageQuote = $this->manageQuote->load($params['request_entity_id']);
                if (!$manageQuote->getQuoteId()) {
                    $this->messageManager->addErrorMessage(__('We can\'t find a quote.'));
                    return $this->resultRedirectFactory->create()->setPath('*/*/history');
                }
                $status = $manageQuote->getStatus();
                $this->getStatusCanEdit($status, $params);

                $quote = $this->quoteRepository->get($manageQuote->getQuoteId());
                $this->manageQuote->load($params['request_entity_id']);
                if (isset($params['change_shipping_info'])
                    && $params['change_shipping_info']
                    && $this->helperQuoteAddress->isRequiredAddress()
                ) {
                    $this->updateQuote = 1;
                    $this->saveShippingInformation($params, $quote);
                }
                $data = $this->updateItems($params['quote'], $quote, $manageQuote);
                $data['comment'] = $params['customer_note'];
                $this->quoteVersion->setData($data);
                $this->quoteVersion->save();
                $quote->collectTotals();
                $this->quoteRepository->save($quote);
               // $manageQuote->setMoveCheckout(1);
               // $manageQuote->save();

                // Set result data and pass back
                $result = $this->resultJsonFactory->create();

                if (!$this->customerSession->getCustomer()->getId()) {
                    $result->setData(['error' => __('Invalid session ID')]);
                }

                //remove all cart items
                $cart = $this->cart;
                $cartItems = $this->checkoutSession->getQuote()->getItemsCollection();
                foreach($cartItems as $cartItem)
                {
                $cart->removeItem($cartItem->getId()); 
                }
          
                //add all items to cart
                $items = $quote->getAllItems();
                foreach ($items as $item) {   
                    
                    $productId = $this->product->getIdBySku($item->getSku());
                    $newProduct = $this->productFactory->create()->load($productId);
                    $newProduct->setData('custom_overwrite_price', $item->getPrice());
                    // $newProduct->setCustomPrice($item->getPrice());
                    // $newProduct->setOriginalCustomPrice($item->getPrice());
                    // $newProduct->getProduct()->setIsSuperMode(true);

                    $params = array(
                        'form_key' => $this->formKey->getFormKey(),
                        'product'  => $productId, 
                        'qty'      => $item->getQty()
                    );  

                    $newProduct->save();
                    $this->cart->addProduct($newProduct, $params);
                }
                $this->cart->save();
                

              

            } catch (\Exception $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
                return $this->resultRedirectFactory
                    ->create()
                    ->setPath('*/*/view/quote_id/' . $params['request_entity_id']);
            }
        }
        $params = [
            'token' => $this->manageQuote->getToken(),
            'quote' => $this->manageQuote->getId(),
            '_secure' => true
        ];

        return $this->resultRedirectFactory->create()->setPath('checkout/cart/', $params);
    }

    /**
     * Get Status Can Edit
     *
     * @param string $status
     * @param array $params
     * @return $this|Redirect
     */
    private function getStatusCanEdit($status, $params)
    {
        $disableResubmit = $this->helper->disableResubmit();
        if (!$disableResubmit) {
            $statusCanEdit = [
                Status::STATE_UPDATED,
                Status::STATE_REJECTED,
                Status::STATE_EXPIRED
            ];
        } else {
            $statusCanEdit = [
                Status::STATE_UPDATED
            ];
        }
        if (!in_array($status, $statusCanEdit)) {
            $this->messageManager->addErrorMessage(__("This Quote can't update."));
            return $this->resultRedirectFactory
                ->create()
                ->setPath('*/*/view/quote_id/' . $params['request_entity_id']);
        }
        return $this;
    }

    /**
     * Check has qty = 0
     *
     * @param array $cartData
     * @retrun bool
     */
    public function checkQtyItemZero($cartData)
    {
        foreach ($cartData as $item) {
            if (isset($item["qty"]) && $item["qty"] == 0) {
                return true;
            }
        }
        return false;
    }

}
