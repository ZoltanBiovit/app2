<?php
namespace Ecomwise\ForcedLogin\Model\Config\Source;

class Cms implements \Magento\Framework\Option\ArrayInterface
{
    public function toOptionArray()
    {
        /**
        * @return array
        */
        $res = array();
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $collection = $objectManager->get('\Magento\Cms\Model\ResourceModel\Page\CollectionFactory')->create();
        $collection->addFieldToFilter('is_active' , \Magento\Cms\Model\Page::STATUS_ENABLED); 
        
        foreach($collection as $page){
            $data['value'] = $page->getData('identifier');
            $data['label'] = $page->getData('title');
            $res[] = $data;
        } 
        
        return $res;
    }
}