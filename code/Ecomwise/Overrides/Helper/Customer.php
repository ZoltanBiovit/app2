<?php

namespace Ecomwise\Overrides\Helper;

use Magento\Customer\Api\CustomerRepositoryInterface;

class Customer extends \Magento\Framework\App\Helper\AbstractHelper
{
    /**
     *
     * @var \Magento\Customer\Model\Session $session
     */
    protected $session;

    protected $customerRepository;


    /**
     *
     * @param \Magento\Framework\App\Helper\Context $context
     * @param \Magento\Customer\Model\Session $session
     */
    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Customer\Model\Session $session,
        CustomerRepositoryInterface $customerRepository


    ) {
        $this->session = $session;
        $this->customerRepository = $customerRepository;
		parent::__construct($context);
    }
    
    /**
     *
     * @return boolean
     */
    public function isLoggedIn(){
        return $this->session->isLoggedIn();
    }

    /**
     *
     * @return string
     */
    public function getCustomerId(){
        return $this->session->getCustomerId();
    }

    public function getCustomerGroup(){
        return $this->session->getCustomer()->getGroupId();
    }

    public function getTockicaBodovi($customerId){
        $customer = $this->customerRepository->getById($customerId);
        if($customer->getCustomAttribute('ca_tockica_bodovi')){
            return $customer->getCustomAttribute('ca_tockica_bodovi')->getValue();
        }else{
            return '';
        }
    }

    public function getTockicaStanje($customerId){
        $customer = $this->customerRepository->getById($customerId);
        if($customer->getCustomAttribute('ca_tockica_stanje')){
            return $customer->getCustomAttribute('ca_tockica_stanje')->getValue();
        }else{
            return '';
        }
    }

    public function getTockicaFlag($customerId){
        $customer = $this->customerRepository->getById($customerId);
        return $customer->getCustomAttribute('ca_tockica_flag')->getValue();
    }

    public function getCustomerIndex($customerId){



        $customer = $this->customerRepository->getById($customerId);
        if($customer->getCustomAttribute('ca_customer_index')){
            return $customer->getCustomAttribute('ca_customer_index')->getValue();
        }else{
            return 1;
        }


    }

    public function getCustomerCurrency($customerId){
        


        $customer = $this->customerRepository->getById($customerId);
        if($customer->getCustomAttribute('ca_currency')){
            return $customer->getCustomAttribute('ca_currency')->getValue();
        }else{
            return 1;
        }


    }  
}
?>