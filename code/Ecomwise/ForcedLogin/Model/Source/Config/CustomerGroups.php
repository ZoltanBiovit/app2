<?php
namespace Ecomwise\ForcedLogin\Model\Source\Config;

class CustomerGroups implements \Magento\Framework\Option\ArrayInterface
{
	/**
	 * 
	 * @var \Magento\Customer\Model\ResourceModel\Group\CollectionFactory
	 */
    protected $groupCollectionFactory;
    
    /**
     * 
     * @param \Magento\Customer\Model\ResourceModel\Group\CollectionFactory $groupCollectionFactory
     */
    public function __construct(
    	\Magento\Customer\Model\ResourceModel\Group\CollectionFactory $groupCollectionFactory
    ) {
        $this->groupCollectionFactory = $groupCollectionFactory;
    }
     
    public function toOptionArray()
    {
    	/**
    	 * 
    	 * @var array $statuses
    	 */
    	$statuses = [];
        
        /**
         * 
         * @var \Magento\Customer\Model\ResourceModel\Group\Collection $collection
         */
        $collection = $this->groupCollectionFactory->create();
        
        foreach ($collection as $group) {
            if ($group->getCustomerGroupId() != 0) {
                $statuses [] = ['value'=> $group->getCustomerGroupId(), 'label'=> $group->getCustomerGroupCode()];
            }
        }
        return $statuses;
    }
}
