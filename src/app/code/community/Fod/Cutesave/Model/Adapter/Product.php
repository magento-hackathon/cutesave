<?php

class Fod_Cutesave_Model_Adapter_Product extends Mage_ImportExport_Model_Import_Entity_Product {

    protected $_attributeBlacklist = array(
        'entity_type_id',
        'attribute_set_id',
        'options_container',
        'msrp_enabled',
        'msrp_display_actual_price_type'
    );
    
    protected $_baseStructureArray = array(
    	'_store' => '',
    	'_attribute_set' => '',
     	'_type' => '',
    	'sku' => '',
    	'_product_websites' => '',
    );
    
    protected $_dataRows = array();
    
    protected $_attrSetIdToName = null;
    
    /**
     * Initialize attribute sets code-to-id pairs.
     *
     * @return Mage_ImportExport_Model_Import_Entity_Product
     */
    protected function _getAttributesetNamebyId($id)
    {
    	if($this->_attrSetIdToName === null){
	        foreach (Mage::getResourceModel('eav/entity_attribute_set_collection')
	                ->setEntityTypeFilter($this->_entityTypeId) as $attributeSet) {
	            $this->_attrSetIdToName[$attributeSet->getCode()] = $attributeSet->getId();
	        }
    	}
        return isset($this->_attrSetIdToName[$id]) ? $this->_attrSetIdToName[$id] : '';
    }    

    /**
     * @return Fod_CuteSave_Model_Product_Queue
     */
    protected function _getQueue() {
        return Mage::getSingleton('fod_cutesave/queue');
    }

    /**
     * @param $code
     * @return Mage_Catalog_Model_Resource_Eav_Attribute
     */
    protected function getAttribute($code) {
        static $cache;
        if ( !isset( $cache[ $code ] ) ) {

            $attribute = Mage::getSingleton('eav/config')->getAttribute('catalog_product', $code);
            $cache[ $code ] = $attribute;

        }

        return $cache[ $code ];
    }

    public function convert( Mage_Catalog_Model_Product $product ) {

        $data = array();    
        $data['_store'] = $product->getStoreIds();
        $data['_attribute_set'] = $this->_getAttributesetNamebyId($product->getAttributeSetId());
        $data['_type'] = $product->getTypeId();
        $data['_product_websites'] = $product->getWebsiteIds();

        // Attributes
        foreach(   $product->getData() AS $k => $v ) {
            if ( ( is_string( $v ) || is_numeric( $v ) ) && !in_array( $k, $this->_attributeBlacklist ) ) {

                $data[ $k ] = $v;

            }
        }

        // Stock
        if(is_array($product->getStockData())) {
            $data = array_merge($product->getStockData(), $data);
        }
        
        $this->_addRow($data, $product);
        $this->setCategoryIds($product);
        //$this->setImages($product);

        // TODO: add some magic containing images and options
        return $this->_dataRows;
    }
    

    protected function setImages($product){
        $arr_images = $product->getMediaGalleryImages();
        foreach($arr_images as $image)
        {
            $imagedata = array('_media_image' => $image->getFile(),
                                '_media_is_disabled' =>$image->getDisabled(),
                                '_media_position' => $image->getPosition(),
                                '_media_lable' => $image->getLabel(),
                                '_media_attribute_id' => 703
            );
            $this->_addRow($imagedata, $product);
            //$this->_copyImage($image->getFile());
        }

    }

    protected function setCategoryIds($product){
    	$_categories = $product->getCategoryIds();
    	if(is_array($_categories) && count($_categories)){
        	foreach ($_categories as $categoryId){
        		$data = array();
        		$data['_category'] = $categoryId;
        		$this->_addRow($data, $product);
        	}
        }    	
    }
    
    protected function _addRow($data, $product){
    	$this->_baseStructureArray['_type'] = $product->getTypeId();
    	$this->_baseStructureArray['_attribute_set'] = $this->_getAttributesetNamebyId($product->getAttributeSetId());
    	$data = array_merge($this->_baseStructureArray, $data);
    	
    	$this->_dataRows[] = $data;


    }


    protected function _preparedData() {
        foreach( $this->_getQueue()->getItems() AS $_item ) {
            if ( $_item instanceof Mage_Catalog_Model_Product  ) {
                $this->convert( $_item );
            }
        }
        return $this;
    }

    public function resetData() {
        $this->_dataRows = array();
    }

    public function getData() {
        if ( !count( $this->_dataRows ) ) {
            $this->_preparedData();
        }
        return $this->_dataRows;
    }

    

}