<?php

namespace Wise\PriceCalculation\Plugin;

use Magento\Customer\Model\Session;
use Magento\Framework\App\ResourceConnection;
use Magento\Store\Model\StoreManagerInterface;

/**
 * Class Product change product price
 *
 * Wise\PriceCalculation\Plugin
 */
class Product
{
    /** @var string Pricing tables */
    const INDIVIDUAL_PRICING_TABLE = 'individual_pricing';
    const BRAND_PRICING_TABLE = 'brand_pricing';

    /** @var string Attribute code */
    const ATTRIBUTE_CODE = 'brand';

    /** @var float Customer attributes for price calculation */
    const BIOVIT_VALUTA = 'ca_biovit_valuta';
    const BIOVIT_INDEX = 'ca_biovit_index';

    /**
     * @var ResourceConnection
     */
    private $resource;

    /**
     * @var Session
     */
    private $session;

    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     *
     * @var \Ecomwise\Overrides\Helper\Customer $customerHelper
     */
    protected $customerHelper;

    /**
     * Calculation constructor
     *
     * @param ResourceConnection $resource
     * @param Session $session
     */
    public function __construct(
        ResourceConnection $resource,
        Session $session,
        StoreManagerInterface $storeManager,
        \Ecomwise\Overrides\Helper\Customer $customerHelper
    ) {
        $this->resource = $resource;
        $this->session = $session;
        $this->storeManager = $storeManager;
        $this->customerHelper = $customerHelper;
    }

    /**
     * @param \Magento\Catalog\Model\Product $subject
     * @param $result
     * @return float|int|string
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function afterGetPrice(\Magento\Catalog\Model\Product $subject, $result)
    {
        if (!empty($result)) {
            $connection = $this->resource;
            $tableIndividual = $connection->getTableName(self::INDIVIDUAL_PRICING_TABLE);
            $tableBrand = $connection->getTableName(self::BRAND_PRICING_TABLE);

            if ($this->session->isLoggedIn()) {

                $customerData = $this->session->getCustomerData();
                $customerTaxvat = $customerData->getTaxvat();
                $biovitValuta = $customerData->getCustomAttribute(self::BIOVIT_VALUTA);
                $biovitIndex = $customerData->getCustomAttribute(self::BIOVIT_INDEX);
                
                $customerId = $this->customerHelper->getCustomerId();
                $customerCurrency = $this->customerHelper->getCustomerCurrency($customerId);
                $customerIndex = $this->customerHelper->getCustomerIndex($customerId);
                
                // // zoltan
                // echo $customerTaxvat;
                // echo $biovitValuta;
                // echo $biovitIndex;
                // echo "<br>";
                // // zoltan

                $select = $connection->getConnection()->select()
                    ->from($tableIndividual, ['price'])
                    ->where('customer = ?', $customerTaxvat)
                    ->where('code = ?', $subject->getSku());

                $selectDiscount = $connection->getConnection()->select()
                    ->from($tableIndividual, ['discount'])
                    ->where('customer = ?', $customerTaxvat)
                    ->where('code = ?', $subject->getSku());



                if ($connection->getConnection()->fetchOne($select)) {

                    $discount = $connection->getConnection()->fetchOne($selectDiscount);
                    $resultIdividual = $connection->getConnection()->fetchOne($select);

                    if ($discount > 0) {
                        return ($discount / 100) * $resultIdividual;
                    }

                    return $resultIdividual;
                } else {
                    $brand = $subject->getResource()->getAttributeRawValue(
                        $subject->getId(),
                        self::ATTRIBUTE_CODE,
                        $this->storeManager->getStore()->getId()
                    );
                    $select = $connection->getConnection()->select()
                        ->from($tableBrand, ['indexation', 'currency'])
                        ->where('customer = ?', $customerTaxvat)
                        ->where('supplier = ?', $brand);

                    $selectDiscount  = $connection->getConnection()->select()
                        ->from($tableBrand, ['discount'])
                        ->where('customer = ?', $customerTaxvat)
                        ->where('supplier = ?', $brand);

                    $discount = $connection->getConnection()->fetchRow($selectDiscount);
                    $discountInt = $discount['discount'] ?? 0; 

                    if ($connection->getConnection()->fetchRow($select)) {
                        $record = $connection->getConnection()->fetchRow($select);

                        $resultBrand = $result * $record['indexation'] * $record['currency'];

                        if ($discountInt > 0) {
                            $discountPercent = ($discountInt / 100) * $resultBrand;
                            return $resultBrand - $discountPercent;
                        }

                        return $resultBrand;
                    } elseif ($biovitValuta && $biovitIndex) {

                        return $result * $biovitValuta->getValue() * $biovitIndex->getValue();
                    } else {
                        $resultAttribute = $result * $customerCurrency * $customerIndex;
                        return $resultAttribute;
                    }
                }
            }
        }

        return $result;
    }
}
