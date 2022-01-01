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
 * @package    Bss_StoreCredit
 * @author     Extension Team
 * @copyright  Copyright (c) 2017-2018 BSS Commerce Co. ( http://bsscommerce.com )
 * @license    http://bsscommerce.com/Bss-Commerce-License.txt
 */

namespace Bss\StoreCredit\Controller\Cart;

use Bss\StoreCredit\Helper\Data;
use Bss\StoreCredit\Model\CreditFactory;
use Magento\Checkout\Model\CartFactory;
use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\Pricing\PriceCurrencyInterface;
use Magento\Quote\Api\CartRepositoryInterface;

/**
 * Class UpdatePost
 *
 * @package Bss\StoreCredit\Controller\Cart
 * @SuppressWarnings(PHPMD.AllPurposeAction)
 */
class UpdatePost extends Action
{
    /**
     * @var \Bss\StoreCredit\Helper\Data
     */
    private $bssStoreCreditHelper;

    /**
     * @var \Bss\StoreCredit\Model\CreditFactory
     */
    private $creditFactory;

    /**
     * Sales quote repository
     *
     * @var \Magento\Quote\Api\CartRepositoryInterface
     */
    private $quoteRepository;

    /**
     * @var \Magento\Framework\Pricing\PriceCurrencyInterface
     */
    private $priceCurrency;

    /**
     * @var \Magento\Checkout\Model\CartFactory
     */
    private $cartFactory;

    /**
     * @param Context $context
     * @param CartFactory $cartFactory
     * @param Data $bssStoreCreditHelper
     * @param CreditFactory $creditFactory
     * @param CartRepositoryInterface $quoteRepository
     * @param PriceCurrencyInterface $priceCurrency
     */
    public function __construct(
        Context $context,
        CartFactory $cartFactory,
        Data $bssStoreCreditHelper,
        CreditFactory $creditFactory,
        CartRepositoryInterface $quoteRepository,
        PriceCurrencyInterface $priceCurrency
    ) {
        parent::__construct($context);
        $this->cartFactory = $cartFactory;
        $this->bssStoreCreditHelper = $bssStoreCreditHelper;
        $this->creditFactory = $creditFactory;
        $this->quoteRepository = $quoteRepository;
        $this->priceCurrency = $priceCurrency;
    }

    /**
     * Used store credit action
     *
     * @return \Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\ResultInterface
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function execute()
    {
        if ($this->bssStoreCreditHelper->getGeneralConfig('cart_page_display')) {
            $remove = $this->getRequest()->getParam('remove');
            $amount = (float) $this->getRequest()->getParam('bss_store_credit');
            $cartQuote = $this->cartFactory->create()->getQuote();
            $amount = $this->priceCurrency->round($amount);
            $totals_amount = 0;

            if ($remove) {
                $amount = 0;
            }

            $baseAmount = $this->priceCurrency->round($this->bssStoreCreditHelper->convertBaseFromCurrency($amount));
            $creditModel = $this->creditFactory->create();

            $this->getTotalsAmount($cartQuote, $totals_amount);

            if ($amount < 0 || !$cartQuote->getId() || !$creditModel->validateBalance($cartQuote, $baseAmount)) {
                $this->messageManager->addErrorMessage(__('Something went wrong. Please enter a value again'));
            } elseif ($baseAmount > $totals_amount) {
                $this->messageManager->addErrorMessage(__('Make sure you don\'t apply store credit more than order total.'));
            } else {
                $cartQuote->setBaseBssStorecreditAmountInput($baseAmount);
                $cartQuote->collectTotals();
                $this->quoteRepository->save($cartQuote);
                $this->messageManager->addSuccessMessage(__('Success.'));
            }
        }
        return $this->_redirect($this->_redirect->getRefererUrl());
    }

    /**
     * @param \Magento\Quote\Model\Quote $quote
     * @param float $totals_amount
     * @return float
     */
    private function getTotalsAmount($quote, &$totals_amount)
    {
        $subTotal = 0;
        $totals = $quote->getTotals();
        foreach ($totals as $code => $total) {
            if ($code == 'subtotal') {
                $subTotal += $total->getValue();
                $totals_amount += $total->getValue();
            }
        }
        $shippingAmount = (float) $this->getRequest()->getParam('shipping_amount');
        $taxAmount = (float) $this->getRequest()->getParam('tax_amount');
        if ($shippingAmount) {
            $totals_amount += $shippingAmount;
        }

        if ($taxAmount) {
            $totals_amount += $taxAmount;
        }

        return $subTotal;
    }
}
