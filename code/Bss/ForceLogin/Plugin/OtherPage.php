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
 * @package    Bss_ForceLogin
 * @author     Extension Team
 * @copyright  Copyright (c) 2017-2020 BSS Commerce Co. ( http://bsscommerce.com )
 * @license    http://bsscommerce.com/Bss-Commerce-License.txt
 */
namespace Bss\ForceLogin\Plugin;

use Bss\ForceLogin\Helper\Data;
use Magento\Backend\Model\Auth\Session;
use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Controller\Result\Redirect;
use Magento\Framework\Controller\Result\RedirectFactory;
use Magento\Framework\Message\ManagerInterface;
use Magento\Framework\UrlInterface;
use Magento\Framework\View\Result\Page;

class OtherPage
{
    /**
     * @var Data
     */
    protected $helperData;

    /**
     * @var UrlInterface
     */
    protected $url;
    /**
     * @var ManagerInterface
     */
    protected $messageManager;

    /**
     * @var RedirectFactory
     */
    protected $resultRedirectFactory;
    /**
     * @var Session
     */
    protected $authSession;
    /**
     * @var \Magento\Framework\App\Http\Context
     */
    protected $httpContext;

    /**
     * OtherPage constructor.
     * @param Context $context
     * @param Data $helperData
     * @param Session $authSession
     * @param \Magento\Framework\App\Http\Context $httpContext
     */
    public function __construct(
        Context $context,
        Data $helperData,
        Session $authSession,
        \Magento\Framework\App\Http\Context $httpContext
    )
    {
        $this->helperData = $helperData;
        $this->url = $context->getUrl();
        $this->messageManager = $context->getMessageManager();
        $this->resultRedirectFactory = $context->getResultRedirectFactory();
        $this->authSession = $authSession;
        $this->httpContext = $httpContext;
    }

    /**
     * @param Action $subject
     * @param callable $proceed
     * @param RequestInterface $request
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     * @return Redirect
     */
    public function aroundDispatch(
        Action $subject,
        callable $proceed,
        RequestInterface $request
    ) {
        $result = $proceed($request);
        $resultPage = $result instanceof Page;
        $actionName = $request->getFullActionName();
        $enableLogin = $this->helperData->isEnable();
        $enableOtherPage = $this->helperData->isEnableOtherPage();
        $adminSession = $this->authSession->isLoggedIn();
        $customerLogin = $this->httpContext->getValue(\Magento\Customer\Model\Context::CONTEXT_AUTH);
        if ($this->checkIgnoreRouter($actionName) || !$resultPage) {
            return $result;
        } elseif ($adminSession) {
            return $result;
        } elseif ($enableLogin && $enableOtherPage && !$customerLogin) {
            $resultRedirect = $this->resultRedirectFactory->create();
            $message = $this->helperData->getAlertMessage();
            if ($message) {
                $this->messageManager->addErrorMessage($message);
            }
            return $resultRedirect->setPath('customer/account/login');
        } else {
            return $result;
        }
    }

    /**
     * Check actionName of ignoreRouter
     *
     * @param string $actionName
     * @return bool
     */
    public function checkIgnoreRouter($actionName)
    {
        $actionName = str_replace("_", "/", $actionName);
        $ignoreList = $this->helperData->getIgnoreListRouter();
        $ignoreList = str_replace(" ", "", $ignoreList);
        $ignoreList = str_replace("\t","", $ignoreList);
        $ignoreList = str_replace("\r","", $ignoreList);
        $ignoreList = str_replace("\n", "", $ignoreList);
        $arrayIgnoreList = array_merge(explode(",", $ignoreList), $this->getIgnoreList());
        return in_array($actionName, $arrayIgnoreList) || in_array($actionName . "/", $arrayIgnoreList);
    }

    /**
     * Get IgnoreList
     *
     * @return array
     */
    public function getIgnoreList()
    {
        return [
            'catalog/product/view', 'catalog/category/view', 'checkout/cart/index', 'checkout/index/index', 'search/term/popular',
            'catalogsearch/result/index', 'catalogsearch/advanced/index', 'cms/page/view', 'cms/noroute/index',
            'cms/index/index', 'customer/account/login', 'customer/account/loginPost', 'customer/account/logoutSuccess',
            'customer/account/logout', 'customer/account/resetPassword', 'customer/account/resetPasswordpost',
            'customer/account/index', 'customer/account/forgotpassword', 'customer/account/forgotpasswordpost',
            'customer/account/createPassword', 'customer/account/createpassword', 'customer/account/createPost',
            'adminhtml/index/index', 'adminhtml/noroute/index', 'adminhtml/auth/login', 'adminhtml/dashboard/index',
            'adminhtml/auth/logout', 'contact/index/index', 'customer/account/create'
        ];
    }
}
