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
 * @package    Bss_SalesRep
 * @author     Extension Team
 * @copyright  Copyright (c) 2020 BSS Commerce Co. ( http://bsscommerce.com )
 * @license    http://bsscommerce.com/Bss-Commerce-License.txt
 */
namespace Bss\SalesRep\Plugin\Controller\Adminhtml;

use Bss\SalesRep\Helper\Data;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Authorization;
use Magento\Framework\Controller\Result\Redirect;
use Magento\Framework\Controller\Result\RedirectFactory;
use Magento\Framework\Message\ManagerInterface;
use Magento\Framework\Session\SessionManagerInterface;

/**
 * Class Edit
 *
 * @package Bss\SalesRep\Plugin\Controller\Adminhtml
 */
class Edit
{
    /**
     * @var Data
     */
    protected $helper;

    /**
     * @var RequestInterface
     */
    protected $request;

    /**
     * @var RedirectFactory
     */
    protected $redirect;

    /**
     * @var ManagerInterface
     */
    protected $messageManager;

    /**
     * @var SessionManagerInterface
     */
    protected $coreSession;

    /**
     * @var Authorization
     */
    protected $authorization;

    /**
     * Edit constructor.
     * @param SessionManagerInterface $coreSession
     * @param ManagerInterface $messageManager
     * @param RedirectFactory $redirect
     * @param Data $helper
     * @param RequestInterface $request
     * @param Authorization $authorization
     */
    public function __construct(
        SessionManagerInterface $coreSession,
        ManagerInterface $messageManager,
        RedirectFactory $redirect,
        Data $helper,
        RequestInterface $request,
        Authorization $authorization
    ) {
        $this->coreSession = $coreSession;
        $this->helper = $helper;
        $this->request = $request;
        $this->redirect = $redirect;
        $this->messageManager = $messageManager;
        $this->authorization = $authorization;
    }

    /**
     * Check customer assigned User is Sales Rep.
     *
     * @param \Magento\Customer\Controller\Adminhtml\Index\Edit $edit
     * @param Redirect $result
     * @return Redirect
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function afterExecute(
        \Magento\Customer\Controller\Adminhtml\Index\Edit $edit,
        $result
    ) {
        $customerAllowed = $this->authorization->isAllowed('Magento_Customer::customer');
        $customerIds = $this->coreSession->getCustomerIds();
        $id = $this->request->getParam('id');
        $resultRedirect = $this->redirect->create();
        if (!empty($id) && $this->helper->isEnable()) {
            if ($this->helper->checkUserIsSalesRep() && !in_array($id, $customerIds) && !$customerAllowed) {
                $this->messageManager->addErrorMessage(__('Something went wrong while editing the customer.'));
                $resultRedirect->setPath('salesrep/index/customer');
                return $resultRedirect;
            }
        }
        return $result;
    }
}
