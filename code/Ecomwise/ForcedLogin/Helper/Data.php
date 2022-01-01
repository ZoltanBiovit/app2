<?php
	
namespace Ecomwise\ForcedLogin\Helper;

use Magento\Store\Model\ScopeInterface;

class Data extends \Magento\Framework\App\Helper\AbstractHelper
{
    /**
     *
     * @var \Magento\Framework\App\Http\Context
     */
    protected $contextHttp;

    /**
     *
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $scopeConfig;

    const AMASTY_MODULE_NAME = 'amasty_xsearch/';
    const FORCED_LOGIN_STATUS = 'forcedlogin/parameters/status';
    const FORCED_LOGIN_ACCESS = 'forcedlogin/parameters/access_to_website';
    const FORCED_LOGIN_EXCLUDE_PAGES = 'forcedlogin/parameters/exclude_pages';
    const FORCED_LOGIN_EXCLUDE_CONTACT = 'forcedlogin/parameters/exclude_contact_page';
    const FORCED_LOGIN_ALLOWED_CUSTOMER_GROUPS = 'forcedlogin/parameters/customergroups_ecw';
    const CUSTOMER_SUPPORT_SENDER_EMAIL = 'trans_email/ident_support/email';

    /**
     *
     * @param \Magento\Framework\App\Helper\Context $context
     * @param \Magento\Framework\App\Http\Context $contextHttp
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     */
    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Framework\App\Http\Context $contextHttp,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
    ) {
        parent::__construct($context);
        $this->contextHttp = $contextHttp;
        $this->scopeConfig = $scopeConfig;
    }

    /**
     * Get amasti module configuration
     *
     * @param [type] $path
     * @return string
     */
    public function getAmastyModuleConfig($path)
    {
        return $this->scopeConfig->getValue(self::AMASTY_MODULE_NAME . $path, ScopeInterface::SCOPE_STORE);
    }

    /**
     * Is customer logged in
     *
     * @return boolean
     */
    public function isLoggedIn(){
        $isLoggedIn = $this->contextHttp->getValue(\Magento\Customer\Model\Context::CONTEXT_AUTH);

        return $isLoggedIn;
    }

    /**
     * get forced login parametars
     *
     * @return string
     */
    public function getForcedLoginStatus(){
        $forced_login_status = $this->scopeConfig->getValue(
            self::FORCED_LOGIN_STATUS,
            ScopeInterface::SCOPE_STORE
        );

        return $forced_login_status;
    }

    /**
     * Get forced login access to website
     *
     * @return string
     */
    public function getForcedLoginAccess(){
        $forced_login_access = $this->scopeConfig->getValue(
            self::FORCED_LOGIN_ACCESS,
            ScopeInterface::SCOPE_STORE
        );

        return $forced_login_access;
    }

    /**
     * get excluded pages
     *
     * @return string
     */
    public function getExcludePages(){
        $exclude_pages = $this->scopeConfig->getValue(
            self::FORCED_LOGIN_EXCLUDE_PAGES,
            ScopeInterface::SCOPE_STORE
        );
        
        return $exclude_pages;
    }

    /**
     * Is contact page excluded?
     *
     * @return string
     */
    public function getExcludeContact(){
        $exclude_contact = $this->scopeConfig->getValue(
            self::FORCED_LOGIN_EXCLUDE_CONTACT,
            ScopeInterface::SCOPE_STORE
        );

        return $exclude_contact;
    }

    /**
     * Get allowed customer groups
     *
     * @return string
     */
    public function getAllowedCustomerGroups(){
        $allowed_customer_groups = $this->scopeConfig->getValue(
            self::FORCED_LOGIN_ALLOWED_CUSTOMER_GROUPS,
            ScopeInterface::SCOPE_STORE
        );

        return $allowed_customer_groups;
    }

    /**
     * Get customer support sender email address
     *
     * @return string
     */
    public function getCustomerSupportSenderEmail(){
        $customerSupportSenderEmail = $this->scopeConfig->getValue(
            self::CUSTOMER_SUPPORT_SENDER_EMAIL,
            ScopeInterface::SCOPE_STORE
        );

        return $customerSupportSenderEmail;
    }
    
}