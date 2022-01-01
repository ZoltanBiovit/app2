<?php
namespace Magebees\Attribute\Controller\Adminhtml\Importattribute;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;

class Index extends \Magento\Backend\App\Action
{

    /**
     * @var PageFactory
     */
    protected $resultPageFactory;

    /**
     * @param Context $context
     * @param PageFactory $resultPageFactory
     */
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
        $this->_setActiveMenu('Magebees_Attribute::import');
        $resultPage->getConfig()->getTitle()->prepend(__('Import/Export Attributes'));
        $resultPage->getConfig()->getTitle()->prepend(__('Import Products Attributes'));
        $resultPage->addBreadcrumb(__('Import Products Attributes'), __('Import Products Attributes'));
        return $resultPage;
    }
}
