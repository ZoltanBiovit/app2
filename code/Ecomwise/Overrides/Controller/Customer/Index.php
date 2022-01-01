<?php
namespace Ecomwise\Overrides\Controller\Customer;

class Index extends \Magento\Framework\App\Action\Action { 

/**
 *
 * @var \Ecomwise\Overrides\Helper\Customer $customerHelper
 */
protected $customerHelper;

/**
 * Constructor
 *
 * @param \Magento\Framework\App\Action\Context $context
 * @param \Ecomwise\Overrides\Helper\Customer $customerHelper
 */
public function __construct(
  \Magento\Framework\App\Action\Context $context,
  \Ecomwise\Overrides\Helper\Customer $customerHelper)
{
  $this->customerHelper = $customerHelper;
  parent::__construct($context);
}

/**
 * Execute
 *
 * @return void
 */
public function execute() { 

  $customerId = $this->customerHelper->getCustomerId();
  
    if($this->customerHelper->isLoggedIn() && $this->customerHelper->getTockicaFlag($customerId) == 1){
      $this->_view->loadLayout(); 
      $this->_view->renderLayout(); 
    }
    else{
      return $this->resultRedirectFactory->create()->setPath('customer/account/login/', ['_current' => true]);
    }
  } 
  
}
