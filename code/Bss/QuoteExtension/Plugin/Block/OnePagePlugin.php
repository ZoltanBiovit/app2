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
 * @package    Bss_QuoteExtension
 * @author     Extension Team
 * @copyright  Copyright (c) 2018-2019 BSS Commerce Co. ( http://bsscommerce.com )
 * @license    http://bsscommerce.com/Bss-Commerce-License.txt
 */
namespace Bss\QuoteExtension\Plugin\Block;

use Bss\QuoteExtension\Helper\Data;
use Magento\Catalog\Helper\Product\ConfigurationPool;
use Magento\Checkout\Model\Cart\ImageProvider;
use Magento\Framework\Locale\FormatInterface as LocaleFormat;
use Magento\Framework\Pricing\PriceCurrencyInterface;
use Bss\QuoteExtension\Helper\QuoteExtension\Address;
use Magento\Framework\UrlInterface;
use Magento\Quote\Api\CartTotalRepositoryInterface;

/**
 * Class Address
 *
 * @package Bss\QuoteExtension\Plugin\Block
 */
class OnePagePlugin
{
    /**
     * @var Data
     */
    protected $helper;

    /**
     * @var UrlInterface
     */
    protected $urlBuilder;

    /**
     * @var Address
     */
    protected $helperAddress;

    /**
     * @var PriceCurrencyInterface
     */
    protected $priceCurrency;

    /**
     * @var ConfigurationPool
     */
    protected $configurationPool;

    /**
     * @var ImageProvider
     */
    protected $imageProvider;

    /**
     * @var CartTotalRepositoryInterface
     */
    protected $cartTotalRepository;

    /**
     * OnePagePlugin constructor.
     * @param Data $helper
     * @param UrlInterface $urlBuilder
     * @param Address $helperAddress
     * @param ConfigurationPool $configurationPool
     * @param PriceCurrencyInterface $priceCurrency
     */
    public function __construct(
        Data $helper,
        UrlInterface $urlBuilder,
        Address $helperAddress,
        ConfigurationPool $configurationPool,
        LocaleFormat $localeFormat,
        ImageProvider $imageProvider,
        CartTotalRepositoryInterface $cartTotalRepository,
        PriceCurrencyInterface $priceCurrency
    ) {
        $this->helper = $helper;
        $this->urlBuilder = $urlBuilder;
        $this->helperAddress = $helperAddress;
        $this->configurationPool = $configurationPool;
        $this->localeFormat = $localeFormat;
        $this->imageProvider = $imageProvider;
        $this->cartTotalRepository = $cartTotalRepository;
        $this->priceCurrency = $priceCurrency;
    }

    /**
     * @param $subject
     * @param $result
     * @return mixed
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function afterGetCheckoutConfig($subject, $result)
    {
        $quote = $subject->getQuoteExtension();
        $token = $subject->getQuoteExtensionToken();
        $manageQuote = $subject->getManageQuoteExtension();
        if ($quote && $token && $manageQuote) {
            $data = [
                'token' => $token,
                'quote' => $manageQuote,
                '_secure' => true
            ];
            $result['quoteData'] = $this->getQuoteData($quote);
            $result['quoteItemData'] = $this->getQuoteItemData($quote);
            $result['selectedShippingMethod'] = $this->getSelectedShippingMethod($quote);
            $result['storeCode'] = $this->getStoreCode($quote);
            $result['staticBaseUrl'] = $this->getStaticBaseUrl($quote);
            $result['checkoutUrl'] = $this->urlBuilder->getUrl('quoteextension', $data);
            $result['priceFormat'] = $this->localeFormat->getPriceFormat(
                null,
                $quote->getQuoteCurrencyCode()
            );
            $result['basePriceFormat'] = $this->localeFormat->getPriceFormat(
                null,
                $quote->getBaseCurrencyCode()
            );
            $result['imageData'] = $this->imageProvider->getImages($quote->getId());
            $result['totalsData'] = $this->getTotalsData($quote);
            $result['isRequiredAddress'] = $this->isRequiredAddress();
        }
        return $result;
    }

    /**
     * Get quote Data
     *
     * @var /Magento/Quote/Model/Quote $quote
     * @return array
     */
    protected function getQuoteData($quote)
    {
        $quoteData = [];
        if ($quote->getId()) {
            $quoteData = $quote->toArray();
            $quoteData['is_virtual'] = $quote->getIsVirtual();
        }
        return $quoteData;
    }

    /**
     * Get Quote item data
     *
     * @return array
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @var /Magento/Quote/Model/Quote $quote
     */
    protected function getQuoteItemData($quote)
    {
        $quoteItemData = [];
        $quoteId = $quote->getId();
        if ($quoteId) {
            $quoteItems = $this->helperAddress->getListItemsById($quoteId);
            foreach ($quoteItems as $index => $quoteItem) {
                $quoteItemData[$index] = $quoteItem->toArray();
                $quoteItemData[$index]['options'] = $this->getFormattedOptionValue($quoteItem);
            }
        }
        return $quoteItemData;
    }

    /**
     * Get shipping methods
     *
     * @var /Magento/Quote/Model/Quote $quote
     * @return array|null
     */
    protected function getSelectedShippingMethod($quote)
    {
        $shippingMethodData = null;
        try {
            $quoteId = $quote->getId();
            $shippingMethod = $this->helperAddress->getShippindMethods($quoteId);
            if ($shippingMethod) {
                $shippingMethodData = $shippingMethod->__toArray();
            }
        } catch (\Exception $exception) {
            $shippingMethodData = null;
        }
        return $shippingMethodData;
    }

    /**
     * Retrieve store code
     *
     * @var /Magento/Quote/Model/Quote $quote
     * @return string
     */
    protected function getStoreCode($quote)
    {
        return $quote->getStore()->getCode();
    }

    /**
     * Get Static Base Url
     *
     * @var /Magento/Quote/Model/Quote $quote
     * @return string
     */
    protected function getStaticBaseUrl($quote)
    {
        return $quote->getStore()->getBaseUrl(UrlInterface::URL_TYPE_STATIC);
    }

    /**
     * Shipping Address is required\
     *
     * @return bool
     */
    protected function isRequiredAddress()
    {
        return $this->helperAddress->isRequiredAddress();
    }

    /**
     * Retrieve formatted item options view
     *
     * @param \Magento\Quote\Api\Data\CartItemInterface $item
     * @return array
     */
    protected function getFormattedOptionValue($item)
    {
        $optionsData = [];
        $options = $this->configurationPool->getByProductType($item->getProductType())->getOptions($item);
        foreach ($options as $index => $optionValue) {
            /* @var $helper \Magento\Catalog\Helper\Product\Configuration */
            $helper = $this->configurationPool->getByProductType('default');
            $params = [
                'max_length' => 55,
                'cut_replacer' => ' <a href="#" class="dots tooltip toggle" onclick="return false">...</a>'
            ];
            $option = $helper->getFormattedOptionValue($optionValue, $params);
            $optionsData[$index] = $option;
            $optionsData[$index]['label'] = $optionValue['label'];
        }
        return $optionsData;
    }

    /**
     * Return quote totals data
     *
     * @return array
     */
    protected function getTotalsData($quote)
    {
        /** @var \Magento\Quote\Api\Data\TotalsInterface $totals */
        $totals = $this->cartTotalRepository->get($quote->getId());
        $items = [];
        /** @var  \Magento\Quote\Model\Cart\Totals\Item $item */
        foreach ($totals->getItems() as $item) {
            $items[] = $item->__toArray();
        }
        $totalSegmentsData = [];
        /** @var \Magento\Quote\Model\Cart\TotalSegment $totalSegment */
        foreach ($totals->getTotalSegments() as $totalSegment) {
            $totalSegmentArray = $totalSegment->toArray();
            if (is_object($totalSegment->getExtensionAttributes())) {
                $totalSegmentArray['extension_attributes'] = $totalSegment->getExtensionAttributes()->__toArray();
            }
            $totalSegmentsData[] = $totalSegmentArray;
        }
        $totals->setItems($items);
        $totals->setTotalSegments($totalSegmentsData);
        $totalsArray = $totals->toArray();
        if (is_object($totals->getExtensionAttributes())) {
            $totalsArray['extension_attributes'] = $totals->getExtensionAttributes()->__toArray();
        }
        return $totalsArray;
    }
}
