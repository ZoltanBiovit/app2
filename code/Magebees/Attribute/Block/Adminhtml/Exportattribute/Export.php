<?php
namespace Magebees\Attribute\Block\Adminhtml\Exportattribute;

class Export extends \Magento\Backend\Block\Template
{
    protected $attrdata;
    
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Catalog\Model\Product\AttributeSet\Options $attrdata
    ) 
{ 
     
     
     
     
     
     
     
     
     
    
        parent::__construct($context);
        $this->attrdata = $attrdata;
    }
    
    public function getAttributeSet()
    {
        return $this->attrdata->toOptionArray();
    }
}
