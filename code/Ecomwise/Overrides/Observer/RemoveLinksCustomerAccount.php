<?php

namespace Ecomwise\Overrides\Observer;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;

class RemoveLinksCustomerAccount implements ObserverInterface{

    protected $helper;
    
    public function __construct(
        \Ecomwise\Overrides\Helper\Customer $helper
        )
    {
            $this->helper = $helper;          
    }

    public function execute(Observer $observer){

        if($this->helper->isLoggedIn()){
            $customerId = $this->helper->getCustomerId();
            $tockicaFlag = $this->helper->getTockicaFlag($customerId);
     
             $layout = $observer->getLayout();
             $blockInvoices = $layout->getBlock('customer-account-navigation-tockica');
      
           
             if ($blockInvoices) {
                 if ($tockicaFlag == 0) {
                     $layout->unsetElement('customer-account-navigation-tockica');
                 }
             }

        }
       
      


    }
}