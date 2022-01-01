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

class Import extends \Magento\Backend\App\Action
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
        \Magento\Eav\Model\Entity\Attribute\Set $eavset,
        \Magento\Eav\Model\Entity\Attribute $attributeoption,
        \Magento\Eav\Model\Config $eavconfig,
        \Magento\Eav\Model\Entity\Attribute\Source\Table $eavtable,
        \Magento\Swatches\Model\Plugin\EavAttribute $colors,
        \Magento\Swatches\Model\Swatch $swatch,
        \Magento\Swatches\Helper\Media $swtchmedia,
        array $data = array()
    ) 
{ 
     
        parent::__construct($context);
        $this->resultPageFactory = $resultPageFactory;
  
        $this->eavset = $eavset;
        $this->attributeoption = $attributeoption;
        $this->eavconfig = $eavconfig;
        $this->eavtable = $eavtable;
        $this->colors = $colors;
        $this->swatch = $swatch;
        $this->swtchmedia = $swtchmedia;
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

			if(isset($formdata['pointer_next']) && $formdata['pointer_next']!=1){
				$flag = false;
				fseek($handle,$formdata['pointer_next']);
			}else{
				$flag = true;
			}

			$count = 1;
	        $attributeData = array();
            $converted_data = array();
            while ($data = fgetcsv($handle, filesize($filename))) {
				if($count > 50){
					break;
				}
               	if (!empty($data)) {
                    try {
                        $entityType = 4;
                        $converted_data['is_visible'] = 0;
                        $converted_data['is_searchable'] = 0;
                        $converted_data['is_required'] = 0;
                        $converted_data['is_unique'] = 0;
                        $converted_data['note'] = 0;
                        $converted_data['is_filterable'] = 0;
                        $converted_data['is_comparable'] = 0;
                        $converted_data['is_visible_on_front'] = 0;
                        $converted_data['is_html_allowed_on_front'] = 0;
                        $converted_data['used_in_product_listing'] = 0;
                        $converted_data['is_used_for_price_rules'] = 0;
                        $converted_data['is_visible_in_advanced_search'] = 0;
                        $converted_data['search_weight'] = 1;
                        $converted_data['is_used_for_promo_rules'] = 0;
                        $converted_data['is_used_in_grid'] = 0;
                        $converted_data['is_visible_in_grid'] = 0;
                        $converted_data['is_filterable_in_grid'] = 0;
                        $myValue = array();
                        foreach ($data as $key => $value) {
                            $converted_data[$this->mappings[$key]] = addslashes($value);
                        }
                    
                        if (isset($converted_data['attribute_code']) && $converted_data['attribute_code'] != "") {
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
                            
                            $attributeGroup = trim($converted_data['attribute_group']);
                            if($attributeGroup){
                                $groupModel = $this->_objectManager->create('\Magento\Eav\Model\Entity\Attribute\Group')->setAttributeGroupName($attributeGroup)->setAttributeSetId($attributeSetId);
                                if ($groupModel->itemExists()) {
                                } else {
                                    $groupModel->save();
                                }
                            }                        
                                       
                            $attrCode = trim($converted_data['attribute_code']);
                            if (isset($converted_data['attribute_label'])) {
                                $attribute_label = trim($converted_data['attribute_label']);
                            } else {
                                $attribute_label = "";
                            }

                            $product_type = "";
                            
                            if (isset($converted_data['frontend_input'])) {
                                $frontend_input = trim($converted_data['frontend_input']);
                            } else {
                                $frontend_input = "text";
                            }
                                                
                            if ($frontend_input == 'price' || $frontend_input == 'weee') {
                                $scope = 2;
                            } else {
                                if (isset($converted_data['attribute_scope'])) {
                                    $scope = $converted_data['attribute_scope'];
                                } else {
                                    $scope = 0;
                                }
                            }
                            
                            if ($frontend_input == 'weee' || $frontend_input == 'media_image') {
                                $attr_required = 0;
                                $is_searchable = 0;
                                $is_comparable = 0;
                                $is_used_for_promo_rules = 0;
                                $is_visible_on_front = 0;
                                $is_visible_in_advanced_search = 0;
                                $is_html_allowed_on_front = 0;
                                $used_in_product_listing = 0;
                                $default_value = '';
                                $search_weight = "1";
                                $is_used_for_price_rules = 0;
                                $is_required_in_admin_store = 0;
                            } else {
                                if (isset($converted_data['default_value'])) {
                                    $default_value = trim($converted_data['default_value']);
                                } else {
                                    $default_value = '';
                                }

                                $attr_required = trim($converted_data['is_required']);
                                $is_searchable= trim($converted_data['is_searchable']);
                                $is_comparable= trim($converted_data['is_comparable']);
                                $attr_unique = trim($converted_data['is_unique']);
                                $is_used_for_promo_rules = trim($converted_data['is_used_for_promo_rules']);
                                $is_visible_on_front = trim($converted_data['is_visible_on_front']);
                                $is_visible_in_advanced_search = trim($converted_data['is_visible_in_advanced_search']);
                                $is_html_allowed_on_front = trim($converted_data['is_html_allowed_on_front']);
                                $used_in_product_listing = trim($converted_data['used_in_product_listing']);
                                $search_weight = $converted_data['search_weight'];
                                $is_used_for_price_rules = $converted_data['is_used_for_price_rules'];
                                if (isset($converted_data['is_required_in_admin_store'])) {
                                    $is_required_in_admin_store = $converted_data['is_required_in_admin_store'];
                                } else {
                                    $is_required_in_admin_store = 0;
                                }
                            }
                            
                            if ($frontend_input == 'select' || $frontend_input == 'multiselect' || $frontend_input == 'price') {
                                $is_filterable= trim($converted_data['is_filterable']);
                            } else {
                                $is_filterable = 0;
                            }
                            
                            if (isset($converted_data['is_filterable_in_search'])) {
                                if ($frontend_input == 'select' || $frontend_input == 'multiselect' || $frontend_input == 'price') {
                                    $is_filterable_in_search = trim($converted_data['is_filterable_in_search']);
                                } else {
                                    $is_filterable_in_search = 0;
                                }
                            } else {
                                $is_filterable_in_search = 0;
                            }
                
                            if (isset($converted_data['position'])) {
                             	if ($frontend_input == 'select' || $frontend_input == 'multiselect' || $frontend_input == 'price') {
                                  	$position = trim($converted_data['position']);
                              	 } else {
                                 	 $position = '0';
                            	 }
                              
                            } else {
                                $position = '0';
                            }
                            
                            if (isset($converted_data['is_wysiwyg_enabled'])) {
                                if ($frontend_input == 'textarea') {
                                    $is_wysiwyg_enabled = trim($converted_data['is_wysiwyg_enabled']);
                                } else {
                                    $is_wysiwyg_enabled = 0;
                                }
                            } else {
                                $is_wysiwyg_enabled = 0;
                            }
                            
                            if (isset($converted_data['used_for_sort_by'])) {
                                if ($frontend_input == 'textarea' || $frontend_input == 'multiselect' || $frontend_input == 'weee' || $frontend_input == 'media_image') {
                                    $used_for_sort_by = 0;
                                } else {
                                    $used_for_sort_by = trim($converted_data['used_for_sort_by']);
                                }
                            } else {
                                $used_for_sort_by = 0;
                            }
                            
                            if (isset($converted_data['frontend_class'])) {
                                if ($frontend_input == 'text') {
                                    if (trim($converted_data['frontend_class']) == '' || trim($converted_data['frontend_class'] == 0)) {
                                        $frontend_class = '';
                                    }

                                    if (trim($converted_data['frontend_class']) == 1) {
                                        $frontend_class = 'validate-number';
                                    }

                                    if (trim($converted_data['frontend_class']) == 2) {
                                        $frontend_class = 'validate-digits';
                                    }

                                    if (trim($converted_data['frontend_class']) == 3) {
                                        $frontend_class = 'validate-email';
                                    }

                                    if (trim($converted_data['frontend_class']) == 4) {
                                        $frontend_class = 'validate-url';
                                    }

                                    if (trim($converted_data['frontend_class']) == 5) {
                                        $frontend_class = 'validate-alpha';
                                    }

                                    if (trim($converted_data['frontend_class']) == 6) {
                                        $frontend_class = 'validate-alphanum';
                                    }
                                } else {
                                    if (trim($converted_data['frontend_class']) == '' || trim($converted_data['frontend_class'] == 0)) {
                                        $frontend_class = '';
                                    }else{
									 	$frontend_class = '';
									}
                                }
                            } else {
                                $frontend_class = '';
                            }
                            
                            if ($frontend_input == 'select' || $frontend_input == 'boolean') {
                                $backend_type = "int";
                            } elseif ($frontend_input == 'price') {
                                $backend_type = "decimal";
                            } elseif ($frontend_input == 'textarea') {
                                $backend_type = "text";
                            } elseif ($frontend_input == 'date') {
                                $backend_type = "datetime";
                            } elseif ($frontend_input == 'weee') {
                                $backend_type = "static";
                            } else {
                                $backend_type = "varchar";
                            }
                            
                            if ($frontend_input == 'multiselect') {
                                $backend_model = "Magento\Eav\Model\Entity\Attribute\Backend\ArrayBackend";
                            } elseif ($frontend_input == 'price') {
                                $backend_model = "Magento\Catalog\Model\Product\Attribute\Backend\Price";
                            } elseif ($frontend_input == 'date') {
                                $backend_model = "Magento\Eav\Model\Entity\Attribute\Backend\Datetime";
                            } elseif ($frontend_input == 'weee') {
                                $backend_model = "Magento\Weee\Model\Attribute\Backend\Weee\Tax";
                            } else {
                                $backend_model = "";
                            }
                            
                            if ($frontend_input == 'date') {
                                $frontend_model = "Magento\Eav\Model\Entity\Attribute\Frontend\Datetime";
                            } else {
                                $frontend_model = "";
                            }
                            
                            if ($frontend_input == 'boolean') {
                                $source_model = "Magento\Eav\Model\Entity\Attribute\Source\Boolean";
                            } elseif ($frontend_input == 'select') {
                                $source_model = "Magento\Eav\Model\Entity\Attribute\Source\Table";
                            } else {
                                $source_model = "";
                            }
                            
                            if (!isset($converted_data['update_product_preview_image'])) {
                                $converted_data['update_product_preview_image'] = 0;
                            }

                            if (!isset($converted_data['use_product_image_for_swatch'])) {
                                $converted_data['use_product_image_for_swatch'] = 0;
                            }

                            $additional_data = "";
                            $swatch_visual = "";
                            $swatch_text = "";
                            
                            if ($frontend_input == 'swatch_visual') {
                                $additional_data = array("swatch_input_type" => 'visual' , "update_product_preview_image" => $converted_data['update_product_preview_image']  , "use_product_image_for_swatch"  => $converted_data['use_product_image_for_swatch'] );
                                $swatch_visual = "yes";

                                
                                $versionData = $this->_objectManager->create('Magento\Framework\App\ProductMetadataInterface');
                                 
								$additional_data = $this->_objectManager->get('Magento\Framework\Serialize\Serializer\Json')->serialize($additional_data);
                                
                                
                                $frontend_input = "select";
                            }
                            
                            if ($frontend_input == 'swatch_text') {
                                $additional_data = array("swatch_input_type" => 'text' , "update_product_preview_image" => $converted_data['update_product_preview_image']  , "use_product_image_for_swatch"  => '0' );
                                $swatch_text = "yes";
                                                            
                                $versionData = $this->_objectManager->create('Magento\Framework\App\ProductMetadataInterface');
                                
								$additional_data = $this->_objectManager->get('Magento\Framework\Serialize\Serializer\Json')->serialize($additional_data);
                                
								$frontend_input = "select";
                            }
                                                         
                            $attributeData = array(
                                'entity_type_id'=>$entityType,
                                'attribute_code'=>$attrCode,
                                'backend_type'=>$backend_type,
                                'frontend_input'=>$frontend_input,
                                'backend_model' => $backend_model,
                                'frontend_model'=> $frontend_model,
                                'source_model'=>  $source_model,
                                'is_global'=>$scope,
                                'is_required'=>$converted_data['is_required'],
                                'is_unique'=>$converted_data['is_unique'],
                                'note'=>$converted_data['note'],
                                'apply_to'  => '',
                                'visible' => 1,
                                'is_user_defined' => 1,
                                'is_wysiwyg_enabled' =>$is_wysiwyg_enabled,
                                'is_visible_on_front' => $is_visible_on_front,
                                'is_searchable'        => $converted_data['is_searchable'],
                                'is_filterable'        => $is_filterable,
                                'is_comparable'        => $converted_data['is_comparable'],
                                'is_used_for_promo_rules' => $is_used_for_promo_rules,
                                'is_visible_in_advanced_search' => $is_visible_in_advanced_search,
                                'is_html_allowed_on_front' => $is_html_allowed_on_front,
                                'used_in_product_listing' => $used_in_product_listing,
                                'used_for_sort_by' => $used_for_sort_by,
                                'is_used_for_price_rules' =>$is_used_for_price_rules,
                                'is_required_in_admin_store' => $is_required_in_admin_store,
                                'is_used_in_grid' => $converted_data['is_used_in_grid'],
                                'is_visible_in_grid' => $converted_data['is_visible_in_grid'],
                                'is_filterable_in_grid' => $converted_data['is_filterable_in_grid'],
                                'is_filterable_in_search' => $is_filterable_in_search,
                                'position' => $position,
                                'frontend_class' => $frontend_class,
                                'search_weight' => $search_weight,
                                'default_value' => $default_value,
                                'frontend_label' => $attribute_label,
                                
                               );
                                                    
                            $attributeModelId = $this->_objectManager->create('Magento\Catalog\Model\ResourceModel\Eav\Attribute')->getCollection()->addFieldToFilter('attribute_code', trim($attrCode))->addFieldToFilter('entity_type_id', $entityType)->setPageSize(1)->load()->getFirstItem();
				                         
                            $attributeModel = $this->_objectManager->create('Magento\Catalog\Model\ResourceModel\Eav\Attribute');
                            
                            $attributeId = $attributeModelId->getId();
                            if ($attributeId) {
                                $attributeModel->load($attributeId);
                            }

                             $attributeModel->addData($attributeData);
                            if($attributeSetId){
                                 $attributeModel->setAttributeSetId($attributeSetId);
                            }

                            $groupCollection = $this->_objectManager->create('Magento\Eav\Model\ResourceModel\Entity\Attribute\Group\Collection')->setAttributeSetFilter($attributeSetId)
    ->addFieldToFilter('attribute_group_name', $attributeGroup)->setPageSize(1)->load();
                            $group = $groupCollection->getFirstItem();        
                            $groupId = $group->getId();
                            if($groupId){
                                $attributeModel->setAttributeGroupId($groupId);
                            }
                            
                            $attributeModel->save();
                            $attributeId = $attributeModel->getId();
                        
                            if ($swatch_visual != "" || $swatch_text != "") {
                                $additionaldata  = $this->attributeoption->load($attributeId);
                                $additionaldata->setData('additional_data', $additional_data)->save();
                            }
                               
                            if ($frontend_input == 'select' || $frontend_input == 'multiselect') {
                                if (isset($converted_data['attribute_options']) || $converted_data['storeview_options']) {
                                    $attribute_options = stripslashes($converted_data['attribute_options']);
                                    $myValue = explode(",", $attribute_options);
									if(isset($converted_data['storeview_options'])){
										$storeview_options = stripslashes($converted_data['storeview_options']);
										$storeview_options_first = explode('|', $storeview_options);
									}else{
										$storeview_options = array();
										$storeview_options_first =  array();
									}                                    
                                   
                                    $storeview_options_second=array();
                                    $storeview_options_final=array();
                                    $m=0;
                                        
                                    foreach ($storeview_options_first as $data) {
                                        $storeview_options_second[] = explode(',', $data);
                                    }
                                                                        
                                    for ($a=0; $a<count($storeview_options_second); $a++) {
                                        foreach ($storeview_options_second[$a] as $data_first) {
                                            $data_final=explode(":", $data_first);
                                            if (array_key_exists("1",$data_final)) {
												if ($data_final[0] != "") {
													$storeview_options_final[$m][$data_final[0]]= stripslashes($data_final[1]);
												}
											}
                                        }

                                        $m++;
                                    }

                                    for ($i=0; $i<count($myValue); $i++) {
                                        $attribute  = $this->attributeoption->load($attributeId);
                                        if (!$this->attributeValueExists($attrCode, $myValue[$i])) {
                                            $storeview_options_final[$i][0]= ($myValue[$i]);
                                            $result = array('value' =>  array( 'option' => $storeview_options_final[$i] ));
                                            $attribute->setData('option', $result);
                                            $attribute->save();
                                        }
                                    }
                                }
                                
                                if ($converted_data['frontend_input'] == "swatch_text") {
                                    if (isset($converted_data['swatch_text_options'])) {
                                        if ($converted_data['swatch_text_options'] != "") {
                                            $textoptions = explode("|", $converted_data['swatch_text_options']);
                                            if (!empty($textoptions)) {
                                                $text_label_data = array();
                                                $storeview_text_options = array();
                                                foreach ($textoptions as $textoption) {
                                                    $text_label_data[] = explode(",", $textoption);
                                                }

                                                $f= 0;
                                                for ($a=0; $a<count($text_label_data); $a++) {
                                                    foreach ($text_label_data[$a] as $text_data_first) {
                                                        $text_data_final=explode(":", $text_data_first);
                                                        $storeview_text_options[$f][$text_data_final[0]]=stripslashes($text_data_final[1]);
                                                    }

                                                    $f++;
                                                }

                                                $w = 0;
                                                foreach ($storeview_text_options as $storeview_text_first) {
                                                    $swtchtxtoptions = array();
                                                    foreach ($storeview_text_first as $key => $value) {
                                                        $attributeswtachtext = $this->_objectManager->create('Magento\Eav\Model\Config');
                                                        $attributeswtachtext  = $attributeswtachtext->getAttribute('catalog_product', $attrCode);

                                                        $swatchtext = $this->_objectManager->create('Magento\Swatches\Model\Swatch');
                                                        $swatchCollection = $this->_objectManager->create('Magento\Swatches\Model\ResourceModel\Swatch\Collection')->addFieldtoFilter('option_id', $attributeswtachtext->setStoreId(0)->getSource()->getOptionId($myValue[$w]))->addFieldtoFilter('store_id', $key);
                                                        $swtchtxtoptions = $swatchCollection->getFirstItem()->getData();
														$value = stripslashes($value);
                                                        if (empty($swtchtxtoptions)) {
                                                            $swatchtext->setData('option_id', $attributeswtachtext->setStoreId(0)->getSource()->getOptionId($myValue[$w]));
                                                            $swatchtext->setData('store_id', $key);
                                                            $swatchtext->setData('type', 0);
                                                            $swatchtext->setData('value', $value);
                                                            $swatchtext->save();
                                                        } else {
                                                            $swatchtext = $swatchtext->load($swtchtxtoptions['swatch_id']);
                                                            $swatchtext->setData('store_id', $key);
                                                            $swatchtext->setData('type', 0);
                                                            $swatchtext->setData('value', $value);
                                                            $swatchtext->save();
                                                        }
                                                    }

                                                    $w++;
                                                }
                                            }
                                        }
                                    }
                                }
                                    
                                if ($converted_data['frontend_input'] == "swatch_visual") {
                                    if (isset($converted_data['swatch_options'])) {
                                        if ($converted_data['swatch_options'] != "") {
                                            $swatchoptions = explode("|", $converted_data['swatch_options']);
                                            if (!empty($swatchoptions)) {
                                                $d = 0;
                                                $swtchoption = array();
                                                foreach ($myValue as $val) {
                                                    $attributeswtach = $this->_objectManager->create('Magento\Eav\Model\Config');
                                                    $attributeswtach  = $attributeswtach->getAttribute('catalog_product', $attrCode);
													if (!array_key_exists($d,$swatchoptions)){
												$swatchoptions[$d] = '';
													
													}
													
                                                    if (preg_match('/(\.jpg|\.png|\.bmp)$/', $swatchoptions[$d])) {
                                                        $swchtype = 2;
                                                        $swchvalue = "";

                                                        $filesystem = $this->_objectManager->get('Magento\Framework\Filesystem');
                                                        $reader = $filesystem->getDirectoryRead(\Magento\Framework\App\Filesystem\DirectoryList::MEDIA);
                                                        if (file_exists($reader->getAbsolutePath("attribute/swatch/cws/".$swatchoptions[$d]))) {
                                                            $cwsswatch = $reader->getAbsolutePath("attribute/swatch/cws/".$swatchoptions[$d]);
                                                            $attrswatch = $reader->getAbsolutePath("attribute/swatch/".$swatchoptions[$d]);
                                                            copy($cwsswatch, $attrswatch);
                                                            $this->swtchmedia->generateSwatchVariations($swatchoptions[$d]);
                                                            $swchvalue = "/".$swatchoptions[$d];
                                                        }
                                                    } elseif (preg_match('/^#[a-f0-9]{6}$/i', $swatchoptions[$d])) {
                                                        $swchtype = 1;
                                                        $swchvalue = $swatchoptions[$d];
                                                    } else {
                                                        $swchtype = 0;
                                                        $swchvalue = $swatchoptions[$d];
                                                    }

                                                    $swatch = $this->_objectManager->create('Magento\Swatches\Model\Swatch');
                                                    $swtchoption = $swatch->load($attributeswtach->setStoreId(0)->getSource()->getOptionId($val), 'option_id');
                                                    $swtchoption = $swtchoption->getData();
                                                    if (empty($swtchoption)) {
                                                        $swatch->setData('option_id', $attributeswtach->setStoreId(0)->getSource()->getOptionId($val));
                                                        $swatch->setData('store_id', 0);
                                                        $swatch->setData('type', $swchtype);
                                                        $swatch->setData('value', $swchvalue);
                                                        $swatch->save();
                                                    } else {
                                                        $swatch = $swatch->load($swtchoption['swatch_id']);
                                                        $swatch->setData('type', $swchtype);
                                                        $swatch->setData('value', $swchvalue);
                                                        $swatch->save();
                                                    }
                                                        
                                                    $d++;
                                                }
                                            }
                                        }
                                    }
                                }
                            }
                                
                            if (isset($converted_data['store_label'])) {
                                $store_label = $converted_data['store_label'];
                                if ($store_label) {
                                    $store_label_data = explode("|", $store_label);
                                    $store_label_data_final = array();
                                    for ($i=0; $i<count($store_label_data); $i++) {
                                        $store_label_data_final[] = explode(":", $store_label_data[$i]);
                                    }

                                    $store_label_name = array();
                                    for ($j=0; $j<count($store_label_data_final); $j++) {
                                        $store_label_name[$store_label_data_final[$j][0]] = stripslashes($store_label_data_final[$j][1]);
                                    }

                                    $attribute = $this->attributeoption->load($attributeId);
                                    $attribute->setStoreLabels($store_label_name)->save();
                                }
                            }

                            $myValue= array();
                        }
						
						if ($count == 50) { 
							$csvresult['count'] = $count;
							$csvresult['pointer_last'] = ftell($handle);
							$next = fgets($handle);
							if(!empty($next)){
								$csvresult['no_more'] = false;
							}else{
								$this->messageManager->addSuccess(__('Attributes were successfully Imported.'));
                         		$csvresult['no_more'] = true;
							}
						
							$this->getResponse()->representJson($this->_objectManager->get('Magento\Framework\Json\Helper\Data')->jsonEncode($csvresult));
							return;

						} 		
						
                    } catch (\Exception $e) {
						$error[] = $converted_data['attribute_code']." >> ".$e->getMessage();
				    }
                }
				$count++;	
            }
			$this->messageManager->addSuccess(__('Attributes were successfully Imported.'));
			$csvresult['count'] = $count-1;
			$csvresult['pointer_last'] = ftell($handle);
			$csvresult['no_more'] = true;
          //  fclose($handle);
		
        }	
		
        if (!empty($error)) {
			$writer = new \Zend\Log\Writer\Stream(BP . '/var/log/import_export_attributes.log');
			$logger = new \Zend\Log\Logger();
			$logger->addWriter($writer);
			$logger->info($error);
			$this->messageManager->addError(__('There are some issues while importing the attributes. Please check "var/log/import_export_attributes.log" file.')); 
          
        }
		$this->getResponse()->representJson($this->_objectManager->get('Magento\Framework\Json\Helper\Data')->jsonEncode($csvresult));

        //$this->_redirect('*/*/index');
        //return;
    }
    
    public function attributeValueExists($attrCode, $arg_value)
    {
        $attributeoptions = $this->_objectManager->create('Magento\Eav\Model\Config');
        $attributeoptions = $attributeoptions->getAttribute('catalog_product', $attrCode);
        $options = $attributeoptions->setStoreId(0)->getSource()->getAllOptions(false);
        foreach ($options as $option) {
            if ($option['label'] == $arg_value) {
                return $option['value'];
            }
        }

        return false;
    }
}
