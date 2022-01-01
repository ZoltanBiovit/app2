<?php

namespace Ecomwise\ForcedLogin\Block\Search;

class Login extends \Magento\Framework\View\Element\Template
{
    /**
     *
     * @var \Ecomwise\ForcedLogin\Helper\Data
     */
    protected $forcedLoginHelper;
    
    /**
     *
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Ecomwise\ForcedLogin\Helper\Data $forcedLoginHelper
     */
    public function __construct(
    \Magento\Framework\View\Element\Template\Context $context,
    \Ecomwise\ForcedLogin\Helper\Data $forcedLoginHelper)
	{
        $this->forcedLoginHelper = $forcedLoginHelper;
        $this->setTemplate('Ecomwise_ForcedLogin::search_message.phtml');
		parent::__construct($context);
    }
    
    /**
     * check if customer is logged in
     *
     * @return boolean
     */
    public function isLoggedIn(){
        $isLoggedIn = $this->forcedLoginHelper->isLoggedIn();

        return $isLoggedIn;
    }

    /**
     * Get forced login status
     *
     * @return string
     */
    public function getForcedLoginStatus(){
        $forcedLoginStatus = $this->forcedLoginHelper->getForcedLoginStatus();

        return $forcedLoginStatus;
    }
}