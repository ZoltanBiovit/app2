<?php
namespace Ecomwise\ForcedLogin\Observer;

use Magento\Framework\Event\ObserverInterface;
use Zend\Validator\InArray;

class ForceCustomerLoginObserver implements ObserverInterface
{

    /**
     *
     * @var \Ecomwise\ForcedLogin\Helper\Data $forcedLoginHelper
     */
    private $forcedLoginHelper;

    /**
     *
     * @var \Magento\Customer\Model\Session $customerSession
     */
    private $customerSession;

    /**
     *
     * @var \Magento\Customer\Model\Url $customerUrl
     */
    private $customerUrl;

    /**
     *
     * @var \Magento\Framework\App\Action\Context $context
     */
    private $context;

    /**
     *
     * @var \Magento\Framework\App\Http\Context $contextHttp
     */
    private $contextHttp;

    /**
     *
     * @param \Ecomwise\ForcedLogin\Helper\Data $forcedLoginHelper
     * @param \Magento\Framework\App\Action\Context $context
     * @param \Magento\Customer\Model\Session $customerSession
     * @param \Magento\Framework\App\Http\Context $contextHttp
     * @param \Magento\Customer\Model\Url $customerUrl
     * @param \Magento\Cms\Api\PageRepositoryInterface $pageRepository
     * @param \Magento\Framework\Api\SearchCriteriaBuilder $searchCriteriaBuilder
     */
    public function __construct(
        \Ecomwise\ForcedLogin\Helper\Data $forcedLoginHelper,
        \Magento\Framework\App\Action\Context $context,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Framework\App\Http\Context $contextHttp,
        \Magento\Customer\Model\Url $customerUrl,
        \Magento\Cms\Api\PageRepositoryInterface $pageRepository,
        \Magento\Framework\Api\SearchCriteriaBuilder $searchCriteriaBuilder
    ) {
        $this->forcedLoginHelper     = $forcedLoginHelper;
        $this->context         = $context;
        $this->customerSession = $customerSession;
        $this->customerUrl     = $customerUrl;
        $this->contextHttp     = $contextHttp;
        $this->_cmsPage        = $pageRepository;
        $this->_search         = $searchCriteriaBuilder;
    }

    /**
     * get all pages
     *
     * @return array
     */
    public function getAllPages()
    {
        $pages = [];
        foreach($this->_cmsPage->getList($this->_getSearchCriteria())->getItems() as $page) {
            $pages[] = [
                'value' => $page->getIdentifier(),
            ];
        }
        return $pages;
    }

    /**
     * get search criteria
     *
     * @return void
     */
    protected function _getSearchCriteria()
    {
        return $this->_search->addFilter('is_active', '1')->create();
    }

    /**
     * execute function
     *
     * @param \Magento\Framework\Event\Observer $observer
     * @return void
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $event_name = $observer->getEventName();
        $forced_login_status = $this->forcedLoginHelper->getForcedLoginStatus();
        $forced_login_access = $this->forcedLoginHelper->getForcedLoginAccess();
        $exclude_pages = $this->forcedLoginHelper->getExcludePages();
        $exclude_contact = $this->forcedLoginHelper->getExcludeContact();

        if ($forced_login_status) {
            $module_name     = $this->context->getRequest()->getModuleName();
            $controller_name = $this->context->getRequest()->getControllerName();
            $action_name     = $this->context->getRequest()->getActionName();
            $isLoggedIn = $this->forcedLoginHelper->isLoggedIn();

            if ($isLoggedIn || $module_name === 'api') {
                return $this;
            }
            if (!$isLoggedIn && $module_name === 'amasty_xsearch') {
                return $this;
            }
            if ($this->context->getRequest()->isXmlHttpRequest()) {
                return $this;
            }
            if ($controller_name === 'store' && $action_name === 'redirect') {
                return $this;
            }

            if ($controller_name === 'account' && $forced_login_access === '1') {
                return $this;
            }

            if ($forced_login_access === '0' && $controller_name === 'account'
                && ($action_name === 'login' || $action_name === 'loginPost'
                || $action_name === 'forgotpassword'
                || $action_name === 'createpassword'                )
            ) {
                return $this;
            }

            $requestData = $observer->getEvent()->getData('request');
            $exclude_pages_array = explode(",", $exclude_pages);
            $originalPathinfo = $requestData->getoriginalPathInfo();
            $array = array ('.html', '/');
            $originalPathinfoA = str_replace($array,"",$originalPathinfo);

			if ( !$exclude_pages == ""){
              
            if (in_array("home", $exclude_pages_array) && $originalPathinfoA == ""){
                return $this;
            }

            if (in_array($originalPathinfoA, $exclude_pages_array)){
                return $this;
            }
        }

            if ($exclude_contact && $originalPathinfoA == 'contact'){
                return $this;
            }

            $customer_login_url = $this->customerUrl->getLoginUrl();
            $this->context->getResponse()->setRedirect($customer_login_url);
        }

        return $this;
    }
}