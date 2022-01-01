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
 * @package    Bss_RewardPointGraphQl
 * @author     Extension Team
 * @copyright  Copyright (c) 2020 BSS Commerce Co. ( http://bsscommerce.com )
 * @license    http://bsscommerce.com/Bss-Commerce-License.txt
 */
namespace Bss\RewardPointGraphQl\Model\Resolver;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\GraphQl\Config\Element\Field;
use Magento\Framework\GraphQl\Exception\GraphQlInputException;
use Magento\Framework\GraphQl\Query\Resolver\ContextInterface;
use Magento\Framework\GraphQl\Query\Resolver\Value;
use Magento\Framework\GraphQl\Query\ResolverInterface;
use Magento\Framework\GraphQl\Schema\Type\ResolveInfo;
use Magento\Store\Model\ScopeInterface;
use Bss\RewardPoint\Helper\Data;
use Bss\RewardPoint\Model\Config\Source\Image;

/**
 * Class GetModuleConfigs
 *
 * @package Bss\RewardPointGraphQl\Model\Resolver
 */
class GetModuleConfigs implements ResolverInterface
{
    /**
     * @var ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * @var Data
     */
    protected $bssHelper;

    /**
     * @var \Magento\Framework\View\Asset\Repository
     */
    protected $assetRepo;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;

    /**
     * GetModuleConfigs constructor.
     * @param ScopeConfigInterface $scopeConfig
     * @param Data $bssHelper
     * @param \Magento\Framework\View\Asset\Repository $assetRepo
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     */
    public function __construct(
        ScopeConfigInterface $scopeConfig,
        Data $bssHelper,
        \Magento\Framework\View\Asset\Repository $assetRepo,
        \Magento\Store\Model\StoreManagerInterface $storeManager
    ) {
        $this->scopeConfig = $scopeConfig;
        $this->bssHelper = $bssHelper;
        $this->assetRepo = $assetRepo;
        $this->storeManager = $storeManager;
    }

    /**
     * Resolve
     *
     * @param Field $field
     * @param ContextInterface $context
     * @param ResolveInfo $info
     * @param array|null $value
     * @param array|null $args
     * @return array|Value|mixed
     * @throws GraphQlInputException
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function resolve(Field $field, $context, ResolveInfo $info, array $value = null, array $args = null)
    {
        $result = [];
        if (empty($args['storeview'])) {
            throw new GraphQlInputException(__('"%1" value should be specified.', "storeview"));
        }
        $storeId = $args['storeview'];
        $configs = $this->getConfig($storeId);
        if ($configs) {
            if (isset($configs['general']['maximum_threshold'])) {
                $maximumThreshold = $configs['general']['maximum_threshold'];
            } else {
                $maximumThreshold = null;
            }
            if (isset($configs['general']['expire_day'])) {
                $expireDay = $configs['general']['expire_day'];
            } else {
                $expireDay = null;
            }
            if (isset($configs['general']['maximum_earn_order'])) {
                $maximumEarnOrder = $configs['general']['maximum_earn_order'];
            } else {
                $maximumEarnOrder = null;
            }
            if (isset($configs['earning_point']['maximum_point_order'])) {
                $maximumPointOrder = $configs['earning_point']['maximum_point_order'];
            } else {
                $maximumPointOrder = null;
            }
            if (isset($configs['earning_point']['maximum_earn_review'])) {
                $maximumEarnReview = $configs['earning_point']['maximum_earn_review'];
            } else {
                $maximumEarnReview = null;
            }
            $result['active'] = (int) $configs['general']['active'];
            $result['redeem_threshold'] = $configs['general']['redeem_threshold'];
            $result['maximum_threshold'] = $maximumThreshold;
            $result['expire_day'] = $expireDay;
            $result['earn_tax'] = $configs['earning_point']['earn_tax'];
            $result['earn_shipping'] = $configs['earning_point']['earn_shipping'];
            $result['earn_order_paid'] = $configs['earning_point']['earn_order_paid'];
            $result['maximum_earn_order'] = $maximumEarnOrder;
            $result['maximum_earn_review'] = $maximumEarnReview;
            $result['auto_refund'] = $configs['earning_point']['auto_refund'];
            $result['maximum_point_order'] = $maximumPointOrder;
            $result['allow_spend_tax'] = $configs['spending_point']['allow_spend_tax'];
            $result['allow_spend_shipping'] = $configs['spending_point']['allow_spend_shipping'];
            $result['restore_spent'] = $configs['spending_point']['restore_spent'];
            $result['point_icon'] = $this->getPointIconUrl();
            $result['sw_point_header'] = $configs['frontend']['sw_point_header'];
            $result['point_mess_register'] = $configs['frontend']['point_mess_register'];
            $result['point_subscrible'] = $configs['frontend']['point_subscrible'];
            $result['cart_order_summary'] = $configs['frontend']['cart_order_summary'];
            $result['product_page_tab_review'] = $configs['frontend']['product_page_tab_review'];
            $result['product_page_reward_point'] = $configs['frontend']['product_page_reward_point'];
            $result['cate_page_reward_point'] = $configs['frontend']['cate_page_reward_point'];
            $result['point_slider'] = $configs['frontend']['point_slider'];
            $result['sender'] = $configs['email_notification']['sender'];
            $result['earn_point_template'] = $configs['email_notification']['earn_point_template'];
            $result['spend_point_template'] = $configs['email_notification']['spend_point_template'];
            $result['expiry_warning_template'] = $configs['email_notification']['expiry_warning_template'];
            $result['expire_day_before'] = $configs['email_notification']['expire_day_before'];
            $result['subscrible'] = $configs['email_notification']['subscrible'];
        }
        return $result;
    }

    /**
     * Get module config
     *
     * @param int $websiteId
     * @return mixed
     */
    protected function getConfig($websiteId = null)
    {
        return $this->scopeConfig->getValue(
            'bssrewardpoint',
            \Magento\Store\Model\ScopeInterface::SCOPE_WEBSITE,
            $websiteId
        );
    }

    /**
     * Get Point Icon
     *
     * @return string
     */
    protected function getPointIcon()
    {
        return $this->bssHelper->getValueConfig(Data::XML_PATH_POINT_ICON, ScopeInterface::SCOPE_STORE);
    }

    /**
     * Get url of point icon
     *
     * @return string
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getPointIconUrl()
    {
        $mediaDir = $this->storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA);
        if ($this->getPointIcon() == Image::DEFAULT_ICON_VALUE) {
          $fileId = 'Bss_RewardPoint::' . Image::DEFAULT_ICON_VALUE;
          $paramAssets = [
              'area' => 'frontend'
          ];
            return $this->assetRepo->getUrlWithParams($fileId, $paramAssets);
        }
        if (empty($this->getPointIcon())) {
            return '';
        }
        return $mediaDir . Image::UPLOAD_DIR . "/" . $this->getPointIcon();
    }
}
