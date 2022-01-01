<?php
namespace Magebees\Attribute\Controller\Adminhtml\Importattribute;
use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Setup\InstallDataInterface;
use Magento\Catalog\Model\ResourceModel\Eav\Attribute;
use Magento\Swatches\Model\Swatch;
use Magento\Framework\Exception\InputException;

class ImportSet extends \Magento\Backend\App\Action
{
    protected $resultPageFactory;
    protected $eavset;
    protected $setup;
    protected $attributeoption;
    protected $eavconfig;
    protected $colors;
    protected $mappings = array();

    public function __construct(
        Context $context,
        PageFactory $resultPageFactory,
        array $data = array()
    ){
		parent::__construct($context);
        $this->resultPageFactory = $resultPageFactory;
    }

    public function execute()
    {
        $data = $this->getRequest()->getPost();
        $files =  $this->getRequest()->getFiles();
        $error = array();
		$csvresult = array();
		$formdata = $this->getRequest()->getPost()->toarray();
        if (isset($files['filename']['name']) && $files['filename']['name'] != '') {
            try {
                $uploader = $this->_objectManager->create('Magento\MediaStorage\Model\File\Uploader', array('fileId' => 'filename'));
                $allowed_ext_array = array('csv');
                $uploader->setAllowedExtensions($allowed_ext_array);
                $uploader->setAllowRenameFiles(true);
                $uploader->setFilesDispersion(true);
                $mediaDirectory = $this->_objectManager->get('Magento\Framework\Filesystem')
                    ->getDirectoryRead(DirectoryList::VAR_DIR);
                $result = $uploader->save($mediaDirectory->getAbsolutePath('import/'));
                $path = $mediaDirectory->getAbsolutePath('import');
            } catch (\Exception $e) {
                $this->messageManager->addError(__($e->getMessage()));
                $this->_redirect('*/*/index');
                return;
            }
                    
            $filename = $path.$result['file'];
            $handle = fopen($path.$result['file'], 'r');
            $data = fgetcsv($handle, filesize($filename));
            if (!$this->mappings) {
                $this->mappings = $data;
            }		
	        $attributeData = array();
            $converted_data = array();
            while ($data = fgetcsv($handle, filesize($filename))) {
				
               	if (!empty($data)) {
                    try {
                        $entityType = 4;
                        foreach ($data as $key => $value) {
                            $converted_data[$this->mappings[$key]] = addslashes($value);
                        }
                    
                        if (isset($converted_data['attribute_set_name'])) {
                            $attribute_set_name = "";
                            if (isset($converted_data['attribute_set_name'])) {
                                $attribute_set_name = trim($converted_data['attribute_set_name']);
                                ;
                            }

                            if ($attribute_set_name) {
                                $attributeSet = $this->_objectManager->create('\Magento\Eav\Model\Entity\Attribute\Set');
                                $attributeSet->setEntityTypeId($entityType)->load($attribute_set_name, 'attribute_set_name');
                                
                                $attributeSetId = $this->_objectManager->create('\Magento\Eav\Model\Entity\Attribute\Set')->getCollection()->addFieldToFilter('entity_type_id', $entityType)->addFieldToFilter('attribute_set_name', $attribute_set_name)->getFirstItem();
                                                                        
                                if ($attributeSetId->getId()) {
                                    $attributeSetId = $attributeSetId->getId();
                                }else{
                                    $attributeSet->setAttributeSetName($attribute_set_name)->validate();
                                    $attributeSet->save();
                                    if (isset($converted_data['based_on_attribute_set'])) {
                                        $basedon = trim($converted_data['based_on_attribute_set']);
                                        $basedOnAttribute =  $this->_objectManager->create('\Magento\Eav\Model\Entity\Attribute\Set')->getCollection()->addFieldToFilter('entity_type_id', $entityType)->addFieldToFilter('attribute_set_name', $basedon)->getFirstItem();
                                        
                                        $basedOnId = $basedOnAttribute->getId();
                                         if ($basedOnId) {
                                            $attributeSet->initFromSkeleton($basedOnId)->save();
                                         } 
                                    }
                                    $attributeSetId = $attributeSet->getId();
                                }
                            }
                                   
                        }
									
                    } catch (\Exception $e) {
						$error[] = $converted_data['attribute_set_name']." >> ".$e->getMessage();
				    }
                }			
            }
			//$this->messageManager->addSuccess(__('Attribute Sets were successfully Imported.'));	
			$result = "<div class='message message-success success'><div data-ui-id='messages-message-success'>Attribute Sets were successfully Imported.</div></div>";
			$this->getResponse()->representJson($this->_objectManager->get('Magento\Framework\Json\Helper\Data')->jsonEncode($result));  
        }	
		    
    }
}