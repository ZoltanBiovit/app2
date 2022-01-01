<?php

namespace Wise\GroupedCatalog\ViewModel;

use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\View\Element\Block\ArgumentInterface;
use Magento\Store\Model\ScopeInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;

/**
 * Class GroupedProduct receives a simple product from grouped product
 *
 * Wise\GroupedCatalog\ViewModel
 */
class GroupedProduct implements ArgumentInterface
{
    /** @var string path to request4quote settings */
    const PATH_REQUEST4QUOTE_VALIDATE_QTY = 'bss_request4quote/request4quote_global/validate_qty_product';

    /**
     * @var ScopeConfigInterface
     */
    private $scopeConfig;

    /**
     * GroupedProduct constructor
     *
     * @param ScopeConfigInterface $scopeConfig
     */
    public function __construct(ScopeConfigInterface $scopeConfig)
    {
        $this->scopeConfig = $scopeConfig;
    }

    /**
     * Get children from groped product
     *
     * @param object $product
     * @return array|false
     * @throws NoSuchEntityException
     */
    public function getAssociatedProducts(object $product)
    {
        return $product->getTypeInstance()->getAssociatedProducts($product);
    }

    /**
     * Check Apply Default Quantity Condition When Adding Product to Quote
     *
     * @param int $store
     * @return bool
     */
    public function validateQuantity($store = null)
    {
        return $this->scopeConfig->getValue(
            self::PATH_REQUEST4QUOTE_VALIDATE_QTY,
            ScopeInterface::SCOPE_STORE,
            $store
        );
    }
}
