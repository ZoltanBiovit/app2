<?php
namespace Magebees\Attribute\Controller\Adminhtml\Exportattribute;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;

class Index extends \Magento\Backend\App\Action
{
    protected $resultPageFactory;

    public function __construct(
        Context $context,
        PageFactory $resultPageFactory
    ) 
{ 
     
     
     
     
     
     
     
     
     
    
        parent::__construct($context);
        $this->resultPageFactory = $resultPageFactory;
    }
   
    public function execute()
    {
        $resultPage = $this->resultPageFactory->create();
        $this->_setActiveMenu('Magebees_Attribute::export');
        $resultPage->getConfig()->getTitle()->prepend(__('Import/Export Products Attributes'));
        $resultPage->getConfig()->getTitle()->prepend(__('Export Products Attributes'));
        $resultPage->addBreadcrumb(__('Export Products Attributes'), __('Export Products Attributes'));
        return $resultPage;
    }
}
