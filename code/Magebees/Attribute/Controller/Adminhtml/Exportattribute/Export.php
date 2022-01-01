<?php
namespace Magebees\Attribute\Controller\Adminhtml\Exportattribute;
use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Filesystem\DriverInterface;
class Export extends \Magento\Backend\App\Action
{
    protected $resultPageFactory;
    protected $attrconfig;
    protected $attrset;
    protected $attrgroup;
    protected $swtchtextoption;

    public function __construct(
        Context $context,
        PageFactory $resultPageFactory,
        \Magento\Eav\Model\Config $attrconfig,
        \Magento\Eav\Model\Entity\Attribute\Set $attrset,
        \Magento\Eav\Model\Entity\Attribute\Group $attrgroup,
        \Magento\Swatches\Model\Swatch $swtchoption,
        \Magento\Swatches\Model\ResourceModel\Swatch\Collection $swtchtextoption
    ) 
{ 
      
        parent::__construct($context);
        $this->resultPageFactory = $resultPageFactory;
        $this->attrconfig = $attrconfig;
        $this->attrset = $attrset;
        $this->attrgroup = $attrgroup;
        $this->swtchoption = $swtchoption;
        $this->swtchtextoption = $swtchtextoption;
    }
   
    public function execute()
    {
        $filesystem = $this->_objectManager->get('Magento\Framework\Filesystem');
        $extvardir = $filesystem->getDirectoryWrite(DirectoryList::VAR_DIR);
        $exportdir = '/export';
        $extvardir->create($exportdir);
        $extvardir->changePermissions($exportdir, DriverInterface::WRITEABLE_DIRECTORY_MODE);
        $attrid = $this->getRequest()->getParam('attrids');
        $attributes = $this->attrconfig->getEntityType('catalog_product')->getAttributeCollection()->addSetInfo()->addFieldToFilter('is_user_defined', 1);
						
        $export_file_name = "exportattribute_".date('m-d-Y_h-i-s', time()).".csv";
        $header = array();
        $header['attribute_code'] = '';
        $header['attribute_set_name'] = '';
        $header['attribute_group'] = '';
        $header['frontend_input'] = '';
        $header['frontend_class'] = '';
        $header['is_required'] = '';
        $header['default_value'] = '';
        $header['is_unique'] = '';
        $header['note'] = '';
        $header['attribute_scope'] = '';
        $header['is_visible'] = '';
        $header['is_searchable'] = '';
        $header['is_filterable'] = '';
        $header['is_comparable'] = '';
        $header['is_visible_on_front'] = '';
        $header['is_html_allowed_on_front'] = '';
        $header['is_used_for_price_rules'] = '';
        $header['is_filterable_in_search'] = '';
        $header['used_in_product_listing'] = '';
        $header['used_for_sort_by'] = '';
    //	$header['apply_to'] = '';
        $header['is_visible_in_advanced_search'] = '';
        $header['position'] = '';
        $header['is_wysiwyg_enabled'] = '';
        $header['is_used_for_promo_rules'] = '';
        $header['is_required_in_admin_store'] = '';
        $header['is_used_in_grid'] = '';
        $header['is_visible_in_grid'] = '';
        $header['is_filterable_in_grid'] = '';
        $header['search_weight'] = '';
        $header['attribute_label'] = '';
        $header['store_label'] = '';
        $header['attribute_options'] = '';
        $header['storeview_options'] = '';
        $header['swatch_options'] = '';
        $header['swatch_text_options'] = '';
        $header['update_product_preview_image'] = '';
        $header['use_product_image_for_swatch'] = '';
        $filePath = $filesystem->getDirectoryRead(DirectoryList::VAR_DIR)
            ->getAbsolutePath("export/").$export_file_name;
        $files = fopen($filePath, "a");
        fputCsv($files, array_keys($header));
        fclose($files);
        $file = fopen($filePath, "a");
        $additionalData = '';
        foreach ($attributes as $attr) {
            $attributeCode = $attr->getData('attribute_code');
			
			
            if ($attributeCode != "cost") {
                $attribute_set_info = $attr->getData('attribute_set_info');
                $additionalData = $attr->getData('additional_data');
                if ($attrid == "0") {
                    $attribute_set_data_final =array();
                    $attribute_group_data_final =array();
                    if (count($attribute_set_info) > 0) {
                        $attribute_set_data = array();
                        $attribute_group_data = array();
                        foreach ($attribute_set_info as $key => $value) {
                            $attribute_set_data[] =$key;
                            $attribute_group_data[] = $value['group_id'];
                        }

                        for ($i=0; $i<count($attribute_set_data); $i++) {
                            $attributeSetModels = $this->attrset;
                            $attributeSetModels->load($attribute_set_data[$i]);
                            $attributeSetName  = $attributeSetModels->getAttributeSetName();
                            $attribute_set_data_final[] = $attributeSetName;
                        }

                        for ($i=0; $i<count($attribute_group_data); $i++) {
                            $group  = $this->attrgroup->load($attribute_group_data[$i]);
                            $group_name = $group->getData('attribute_group_name');
                            $attribute_group_data_final[] = $group_name;
                        }
                    } else {
                        $attribute_set_data_final[0] = "";
                        $attribute_group_data_final[0] = "";
                    }
                    
                    $frontend_class = $attr->getData('frontend_class');
            
                    if ($frontend_class == '') {
                        $frontend_class_final = 0;
                    }

                    if ($frontend_class == 'validate-number') {
                        $frontend_class_final = 1;
                    }

                    if ($frontend_class == 'validate-digits') {
                        $frontend_class_final = 2;
                    }

                    if ($frontend_class == 'validate-email') {
                        $frontend_class_final = 3;
                    }

                    if ($frontend_class == 'validate-url') {
                        $frontend_class_final = 4;
                    }

                    if ($frontend_class == 'validate-alpha') {
                        $frontend_class_final = 5;
                    }

                    if ($frontend_class == 'validate-alphanum') {
                        $frontend_class_final = 6;
                    }
                    
                    $allStores = $this->_objectManager->create('\Magento\Store\Model\StoreManagerInterface')->getStores();
                    $store_view_data  = array();
                    $store_view_id  = array();
                    $store_options = array();
                    foreach ($allStores as $_eachStoreId => $val) {
                        $_storeId = $this->_objectManager->create('\Magento\Store\Model\StoreManagerInterface')->getStore($_eachStoreId);
                        $store_view_data[] = $attr->getStoreLabel($_storeId->getCode());
                        $store_view_id[] = $_storeId->getId();
                        $store_options[] = $attr->setStoreId($_storeId->getId())->getSource()->getAllOptions(false);
                    }
                                    

                    $store_label ='';
                    $store_options_data = '';
                    for ($i=0; $i<count($store_view_data); $i++) {
                        if ($i < count($store_view_data)-1) {
                            $store_label .= $store_view_id[$i].":".$store_view_data[$i]."|";
                            $store_options_data .= $store_view_id[$i];
                        } else {
                            $store_label .= $store_view_id[$i].":".$store_view_data[$i];
                        }
                    }
                        
                    $options = $attr->setStoreId(0)->getSource()->getAllOptions(false);
                    $attr_option ='';
                    $swtch_option ='';
                    for ($i=0; $i<count($options); $i++) {
                        if ($i < count($options)-1) {
                            if ($additionalData) {
                                $swtchoption = $this->swtchoption->load($options[$i]['value'], 'option_id');
                                $swtchoption = $swtchoption->getData();
                                if (!empty($swtchoption)) {
                                    $swtch_option .= $swtchoption['value']."|";
                                } else {
                                    $swtch_option .= "|";
                                }
                            }

                            $attr_option .= $options[$i]['label'].",";
                        } else {
                            if ($additionalData) {
                                $swtchoption = $this->swtchoption->load($options[$i]['value'], 'option_id');
                                $swtchoption = $swtchoption->getData();
                                if (!empty($swtchoption)) {
                                    $swtch_option .= $swtchoption['value'];
                                }
                            }

                            $attr_option .= $options[$i]['label'];
                        }
                    }
                    
                    if (!empty($additionalData)) {
                        $versionData = $this->_objectManager->create('Magento\Framework\App\ProductMetadataInterface');
                        $additional_data = $this->_objectManager->get('Magento\Framework\Serialize\Serializer\Json')->unserialize($additionalData);
                    
 
                        $update_product_preview_image = $additional_data['update_product_preview_image'];
                        $use_product_image_for_swatch = $additional_data['use_product_image_for_swatch'];
                        if ($additional_data['swatch_input_type'] == "visual") {
                            $frontend_input = "swatch_visual";
                        } else {
                            $frontend_input = "swatch_text";
                            $swtch_option = "";
                            $storeOptionsSwatch = $attr->setStoreId(0)->getSource()->getAllOptions(false);
                            array_unshift($store_options, $storeOptionsSwatch);
                            array_unshift($store_view_id, "0");
                        }
                    } else {
                        $frontend_input = $attr->getData('frontend_input');
                        $update_product_preview_image = '';
                        $use_product_image_for_swatch = '';
                        $swtch_option = "";
                    }
                    
                    foreach ($store_options as $key => $sub) {
                        $storeview_data_first = count($sub);
                    }
                    
                    $storeview_option_final = '';
                    $storeview_text_options = '';
                    for ($p=0; $p<$storeview_data_first; $p++) {
                        for ($q=0; $q<count($store_view_id); $q++) {
                            $swatchCollection = $this->_objectManager->create('Magento\Swatches\Model\ResourceModel\Swatch\Collection');
                            
                            if ($q < count($store_view_id)-1) {
                                if($store_view_id[$q] != 0){
                                    $storeview_option_final .= $store_view_id[$q].":".$store_options[$q][$p]['label'].",";
                                }

                                if ($additionalData) {
                                    $swatchCollection = $swatchCollection->addFieldtoFilter('option_id', $store_options[$q][$p]['value'])->addFieldtoFilter('store_id', $store_view_id[$q])->getData();
                                    
                                    if (!empty($swatchCollection[0])) {
                                        $storeview_text_options .= $store_view_id[$q].":".$swatchCollection[0]['value'].",";
                                    }else{
                                         $storeview_text_options .= $store_view_id[$q].":,";
                                    }
                                }
                            } else {
                                if ($p < $storeview_data_first-1) {
                                    if($store_view_id[$q] != 0){
                                        $storeview_option_final .= $store_view_id[$q].":".$store_options[$q][$p]['label']."|";
                                    }    
                                    
                                    if ($additionalData) {
                                        $swatchCollection = $swatchCollection->addFieldtoFilter('option_id', $store_options[$q][$p]['value'])->addFieldtoFilter('store_id', $store_view_id[$q])->getData();
                                        if (!empty($swatchCollection[0])) {
                                            $storeview_text_options .= $store_view_id[$q].":".$swatchCollection[0]['value']."|";
                                        }else{
                                            $storeview_text_options .= $store_view_id[$q].":|";
                                        }
                                    }
                                } else {
                                    if($store_view_id[$q] != 0){
                                        $storeview_option_final .= $store_view_id[$q].":".$store_options[$q][$p]['label'];
                                    }

                                    if ($additionalData) {
                                        $swatchCollection = $swatchCollection->addFieldtoFilter('option_id', $store_options[$q][$p]['value'])->addFieldtoFilter('store_id', $store_view_id[$q])->getData();
                                        if (!empty($swatchCollection[0])) {
                                            $storeview_text_options .= $store_view_id[$q].":".$swatchCollection[0]['value'];
                                        }else{
                                             $storeview_text_options .= $store_view_id[$q].":";
                                        }
                                    }
                                }
                            }
                        }
                    }               
                
                    for ($i=0; $i<count($attribute_set_data_final); $i++) {
                        $finaldata = array();
                        $finaldata['attribute_code'] = $attr->getData('attribute_code');
                        $finaldata['attribute_set_name'] = $attribute_set_data_final[$i];
                        $finaldata['attribute_group'] = $attribute_group_data_final[$i];
                        $finaldata['frontend_input'] = $frontend_input;
                        $finaldata['frontend_class'] = $frontend_class_final;
                        $finaldata['is_required'] = $attr->getData('is_required');
                        $finaldata['default_value'] = $attr->getData('default_value');
                        $finaldata['is_unique'] = $attr->getData('is_unique');
                        $finaldata['note'] = $attr->getData('note');
                        $finaldata['attribute_scope'] = $attr->getData('is_global');
                        $finaldata['is_visible'] = $attr->getData('is_visible');
                        $finaldata['is_searchable'] = $attr->getData('is_searchable');
                        $finaldata['is_filterable'] = $attr->getData('is_filterable');
                        $finaldata['is_comparable'] = $attr->getData('is_comparable');
                        $finaldata['is_visible_on_front'] = $attr->getData('is_visible_on_front');
                        $finaldata['is_html_allowed_on_front'] = $attr->getData('is_html_allowed_on_front');
                        $finaldata['is_used_for_price_rules'] = $attr->getData('is_used_for_price_rules');
                        $finaldata['is_filterable_in_search'] = $attr->getData('is_filterable_in_search');
                        $finaldata['used_in_product_listing'] = $attr->getData('used_in_product_listing');
                        $finaldata['used_for_sort_by'] = $attr->getData('used_for_sort_by');
                //		$finaldata['apply_to'] = $attr->getData('apply_to');
                        $finaldata['is_visible_in_advanced_search'] = $attr->getData('is_visible_in_advanced_search');
                        $finaldata['position'] = $attr->getData('position');
                        $finaldata['is_wysiwyg_enabled'] = $attr->getData('is_wysiwyg_enabled');
                        $finaldata['is_used_for_promo_rules'] = $attr->getData('is_used_for_promo_rules');
                        $finaldata['is_required_in_admin_store'] = $attr->getData('is_required_in_admin_store');
                        $finaldata['is_used_in_grid'] = $attr->getData('is_used_in_grid');
                        $finaldata['is_visible_in_grid'] = $attr->getData('is_visible_in_grid');
                        $finaldata['is_filterable_in_grid'] = $attr->getData('is_filterable_in_grid');
                        $finaldata['search_weight'] = $attr->getData('search_weight');
                        $finaldata['attribute_label'] = $attr->getData('frontend_label');
                        $finaldata['store_label'] = $store_label;
                        $finaldata['attribute_options'] = $attr_option;
                        $finaldata['storeview_options'] = $storeview_option_final;
                        $finaldata['swatch_options'] = $swtch_option;
                        $finaldata['swatch_text_options'] = $storeview_text_options;                   $finaldata['update_product_preview_image'] = $update_product_preview_image;
                        $finaldata['use_product_image_for_swatch'] = $use_product_image_for_swatch;
                        fputcsv($file, $finaldata);
                    }
                } else {
                    if (count($attribute_set_info) > 0) {
                        if (array_key_exists($attrid, $attribute_set_info)) {
                            $attribute_set_data = array();
                            $attribute_group_data = array();
                            $whitelist = array($attrid);
                            $attribute_set_info = array_intersect_key($attribute_set_info, array_flip($whitelist));
                            foreach ($attribute_set_info as $key => $value) {
                                $attribute_set_data[] =$key;
                                $attribute_group_data[] = $value['group_id'];
                            }

                            $attribute_set_data_final =array();
                            $attributeSetModels = $this->attrset;
                            $attributeSetModels->load($attrid);
                            $attributeSetName  = $attributeSetModels->getAttributeSetName();
                            $attribute_set_data_final[] = $attributeSetName;
                            $attribute_group_data_final =array();
                            for ($i=0; $i<count($attribute_group_data); $i++) {
                                $group  = $this->attrgroup->load($attribute_group_data[$i]);
                                $group_name = $group->getData('attribute_group_name');
                                $attribute_group_data_final[] = $group_name;
                            }
                            
                            $frontend_class = $attr->getData('frontend_class');
                    
                            if ($frontend_class == '') {
                                $frontend_class_final = 0;
                            }

                            if ($frontend_class == 'validate-number') {
                                $frontend_class_final = 1;
                            }

                            if ($frontend_class == 'validate-digits') {
                                $frontend_class_final = 2;
                            }

                            if ($frontend_class == 'validate-email') {
                                $frontend_class_final = 3;
                            }

                            if ($frontend_class == 'validate-url') {
                                $frontend_class_final = 4;
                            }

                            if ($frontend_class == 'validate-alpha') {
                                $frontend_class_final = 5;
                            }

                            if ($frontend_class == 'validate-alphanum') {
                                $frontend_class_final = 6;
                            }
                            
                            $allStores = $this->_objectManager->create('\Magento\Store\Model\StoreManagerInterface')->getStores();
                            $store_view_data  = array();
                            $store_view_id  = array();
                            $store_options = array();
                            foreach ($allStores as $_eachStoreId => $val) {
                                $_storeId = $this->_objectManager->create('\Magento\Store\Model\StoreManagerInterface')->getStore($_eachStoreId);
                                $store_view_data[] = $attr->getStoreLabel($_storeId->getCode());
                                $store_view_id[] = $_storeId->getId();
                                $store_options[] = $attr->setStoreId($_storeId->getId())->getSource()->getAllOptions(false);
                            }
                            
                            $store_label ='';
                            $store_options_data = '';
                            for ($i=0; $i<count($store_view_data); $i++) {
                                if ($i < count($store_view_data)-1) {
                                    $store_label .= $store_view_id[$i].":".$store_view_data[$i]."|";
                                    $store_options_data .= $store_view_id[$i];
                                } else {
                                    $store_label .= $store_view_id[$i].":".$store_view_data[$i];
                                }
                            }

                            $options = $attr->setStoreId(0)->getSource()->getAllOptions(false);
                            $attr_option ='';
                            $swtch_option ='';
                            for ($i=0; $i<count($options); $i++) {
                                if ($i < count($options)-1) {
                                    if ($additionalData) {
                                        $swtchoption = $this->swtchoption->load($options[$i]['value'], 'option_id');
                                        $swtchoption = $swtchoption->getData();
                                        if (!empty($swtchoption)) {
                                            $swtch_option .= $swtchoption['value']."|";
                                        } else {
                                            $swtch_option .= "|";
                                        }
                                    }

                                    $attr_option .= $options[$i]['label'].",";
                                } else {
                                    if ($additionalData) {
                                        $swtchoption = $this->swtchoption->load($options[$i]['value'], 'option_id');
                                        $swtchoption = $swtchoption->getData();
                                        if (!empty($swtchoption)) {
                                            $swtch_option .= $swtchoption['value'];
                                        }
                                    }

                                    $attr_option .= $options[$i]['label'];
                                }
                            }
                            
                            if ($additionalData) {
                                $versionData = $this->_objectManager->create('Magento\Framework\App\ProductMetadataInterface');
                                $additional_data = $this->_objectManager->get('Magento\Framework\Serialize\Serializer\Json')->unserialize($additionalData);
                                                             
                                $update_product_preview_image = $additional_data['update_product_preview_image'];
                                $use_product_image_for_swatch = $additional_data['use_product_image_for_swatch'];
                                if ($additional_data['swatch_input_type'] == "visual") {
                                    $frontend_input = "swatch_visual";
                                } else {
                                    $swtch_option = "";
                                    $frontend_input = "swatch_text";
                                    $storeOptionsSwatch = $attr->setStoreId(0)->getSource()->getAllOptions(false);
                                    array_unshift($store_options, $storeOptionsSwatch);
                                    array_unshift($store_view_id, "0");
                                }
                            } else {
                                $frontend_input = $attr->getData('frontend_input');
                                $update_product_preview_image = '';
                                $use_product_image_for_swatch = '';
                                $swtch_option = "";
                            }
                                                        
                            foreach ($store_options as $key => $sub) {
                                $storeview_data_first = count($sub);
                            }
                                            
                            $storeview_option_final = '';
                            $storeview_text_options = '';
                            for ($p=0; $p<$storeview_data_first; $p++) {
                                for ($q=0; $q<count($store_view_id); $q++) {
                                    $swatchCollection = $this->_objectManager->create('Magento\Swatches\Model\ResourceModel\Swatch\Collection');
                                    if ($q < count($store_view_id)-1) {
                                        if($store_view_id[$q] != 0){
                                            $storeview_option_final .= $store_view_id[$q].":".$store_options[$q][$p]['label'].",";
                                        }
                                        
                                        if ($additionalData) {
                                            $swatchCollection = $swatchCollection->addFieldtoFilter('option_id', $store_options[$q][$p]['value'])->addFieldtoFilter('store_id', $store_view_id[$q])->getData();
                                            if (!empty($swatchCollection[0])) {
                                                $storeview_text_options .= $store_view_id[$q].":".$swatchCollection[0]['value'].",";
                                            }else{
                                                 $storeview_text_options .= $store_view_id[$q].":,";
                                            }
                                        }
                                    } else {
                                        if ($p < $storeview_data_first-1) {
                                            if($store_view_id[$q] != 0){    
                                                $storeview_option_final .= $store_view_id[$q].":".$store_options[$q][$p]['label']."|";
                                            }
                                            
                                            if ($additionalData) {
                                                $swatchCollection = $swatchCollection->addFieldtoFilter('option_id', $store_options[$q][$p]['value'])->addFieldtoFilter('store_id', $store_view_id[$q])->getData();
                                                if (!empty($swatchCollection[0])) {
                                                    $storeview_text_options .= $store_view_id[$q].":".$swatchCollection[0]['value']."|";
                                                }else{
                                                    $storeview_text_options .= $store_view_id[$q].":|";
                                                }
                                            }
                                        } else {
                                            if($store_view_id[$q] != 0){    
                                                $storeview_option_final .= $store_view_id[$q].":".$store_options[$q][$p]['label'];
                                            }
                                            
                                            if ($additionalData) {
                                                $swatchCollection = $swatchCollection->addFieldtoFilter('option_id', $store_options[$q][$p]['value'])->addFieldtoFilter('store_id', $store_view_id[$q])->getData();
                                                if (!empty($swatchCollection[0])) {
                                                    $storeview_text_options .= $store_view_id[$q].":".$swatchCollection[0]['value'];
                                                }else{
                                                    $storeview_text_options .= $store_view_id[$q].":";
                                                }
                                            }
                                        }
                                    }
                                }
                            }
                            
                            for ($i=0; $i<count($attribute_set_data_final); $i++) {
                                $finaldata = array();
                                $finaldata['attribute_code'] = $attr->getData('attribute_code');
                                $finaldata['attribute_set_name'] = $attribute_set_data_final[$i];
                                $finaldata['attribute_group'] = $attribute_group_data_final[$i];
                                $finaldata['frontend_input'] = $frontend_input;
                                $finaldata['frontend_class'] = $frontend_class_final;
                                $finaldata['is_required'] = $attr->getData('is_required');
                                $finaldata['default_value'] = $attr->getData('default_value');
                                $finaldata['is_unique'] = $attr->getData('is_unique');
                                $finaldata['note'] = $attr->getData('note');
                                $finaldata['attribute_scope'] = $attr->getData('is_global');
                                $finaldata['is_visible'] = $attr->getData('is_visible');
                                $finaldata['is_searchable'] = $attr->getData('is_searchable');
                                $finaldata['is_filterable'] = $attr->getData('is_filterable');
                                $finaldata['is_comparable'] = $attr->getData('is_comparable');
                                $finaldata['is_visible_on_front'] = $attr->getData('is_visible_on_front');
                                $finaldata['is_html_allowed_on_front'] = $attr->getData('is_html_allowed_on_front');
                                $finaldata['is_used_for_price_rules'] = $attr->getData('is_used_for_price_rules');
                                $finaldata['is_filterable_in_search'] = $attr->getData('is_filterable_in_search');
                                $finaldata['used_in_product_listing'] = $attr->getData('used_in_product_listing');
                                $finaldata['used_for_sort_by'] = $attr->getData('used_for_sort_by');
                            //	$finaldata['apply_to'] = $attr->getData('apply_to');
                                $finaldata['is_visible_in_advanced_search'] = $attr->getData('is_visible_in_advanced_search');
                                $finaldata['position'] = $attr->getData('position');
                                $finaldata['is_wysiwyg_enabled'] = $attr->getData('is_wysiwyg_enabled');
                                $finaldata['is_used_for_promo_rules'] = $attr->getData('is_used_for_promo_rules');
                                $finaldata['is_required_in_admin_store'] = $attr->getData('is_required_in_admin_store');
                                $finaldata['is_used_in_grid'] = $attr->getData('is_used_in_grid');
                                $finaldata['is_visible_in_grid'] = $attr->getData('is_visible_in_grid');
                                $finaldata['is_filterable_in_grid'] = $attr->getData('is_filterable_in_grid');
                                $finaldata['search_weight'] = $attr->getData('search_weight');
                                $finaldata['attribute_label'] = $attr->getData('frontend_label');
                                $finaldata['store_label'] = $store_label;
                                $finaldata['attribute_options'] = $attr_option;
                                $finaldata['storeview_options'] = $storeview_option_final;
                                $finaldata['swatch_options'] = $swtch_option;
                                $finaldata['swatch_text_options'] = $storeview_text_options;
                                $finaldata['update_product_preview_image'] = $update_product_preview_image;
                                $finaldata['use_product_image_for_swatch'] = $use_product_image_for_swatch;
                                fputcsv($file, $finaldata);
                            }
                        }
                    }
                }
            }
        }

        fclose($file);
        $download_path=$this->getUrl('*/*/downloadexportedfile', array("file"=>$export_file_name));
        $result = "";
        $result = "<div class='message message-success success'><div data-ui-id='messages-message-success'>Generated csv File : <b style='font-size:12px'><a href='".$download_path."' target='_blank'>".$export_file_name."</a></b></div></div>";
                
        $this->getResponse()->representJson($this->_objectManager->get('Magento\Framework\Json\Helper\Data')->jsonEncode($result));

        // $this->_redirect('*/*/index');
        // return;
    }
}
