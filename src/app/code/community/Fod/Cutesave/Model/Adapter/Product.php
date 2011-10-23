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

        // correct the pathes for image import
        $data = array_merge(
            $data,
            array(
                'image' => $product->getImage(),
                'small_image' => $product->getSmallImage(),
                'thumbnail' => $product->getThumbnail(),
            )
        );


        $this->_addRow($data, $product);
        //$this->setCategoryIds($product);
        //$this->setStockData($product);
        $this->setImages($product);

        if ( $product->getTypeId() == 'configurable') {
            $this->setConfigurableProducts( $product );
        }

        $this->setCustomOptions( $product );

        // TODO: add some magic containing images and options
        return $this->_dataRows;
    }
    

    protected function setCustomOptions( $product ) {
        if ( $product->getCanSaveCustomOptions() ) {
            // TODO: Map Custom-Option Array to Export/Import Stuff

            /*
            $options = array(
                'is_require' => true,
                'sort_order' => '1',
                'title' => 'Größe',
                'type' => 'drop_down',
                'values' => array()
            );
            foreach( $sizes AS $size ) {
                $options['values'][] = array(
                    'price' => 0,
                    'price_type' => 'fixed', // 'percent'
                    'sku' => '',
                    'sort_order' => '0',
                    'title' => $size,
                 );
            }
            */

            foreach( $product->getProductOptions() AS $option ) {

                $option += array_flip( array('type','title','is_required','price', 'sku', 'max_characters', 'sort_order') );

                $row = array();
                $row['_custom_option_store']        = 'default';
                $row['_custom_option_type']	        = $option['type'];
                $row['_custom_option_title']        = $option['title'];
                $row['_custom_option_is_required']  = $option['is_required'];
                $row['_custom_option_price']        = $option['price'];
                $row['_custom_option_sku']	        = $option['sku'];
                $row['_custom_option_max_characters'] = $option['max_characters'];
                $row['_custom_option_sort_order']   = $option['sort_order'];

                $this->_addRow($row, $product);

                foreach( $option['values'] AS $value ) {

                    $row = array();

                    $value += array_flip( array('title','price','sku','sort') );

                    $row['_custom_option_row_title']    = $value['title'];
                    $row['_custom_option_row_price']    = $value['price'];
                    $row['_custom_option_row_sku']	    = $value['sku'];
                    $row['_custom_option_row_sort']     = $value['sort'];

                    $this->_addRow($row, $product);

                }

            }


        }
    }

    protected function setConfigurableProducts($product) {
        if ( $product->getTypeId() != 'configurable') {
            return false;
        }

        $base = array();
        $base['_super_products_sku'] = '';
        $base['_super_attribute_code'] = '';
        $base['_super_attribute_option'] = '';
        $base['_super_attribute_price_corr'] = '';

        /**
         *  prüfen aus welchen attributen configurable products besteht
         *  alle simple abrufen
         *      - je simple mittels convert 1:n rows
         *      - row mit _super* initialisieren
         *
         */
        

    }

    protected function setImages($product){
        $arr_images = $product->getMediaGallery('images');

        foreach($arr_images as $image)
        {
            $imagedata = array('_media_image' =>  $image['file'],
                                '_media_is_disabled' =>$image['disabled'],
                                '_media_position' => $image['position'],
                                '_media_lable' => $image['label'],
                                '_media_attribute_id' => 703,

            );
            $this->_addRow($imagedata, $product);
        }

    }


    protected function setStockData($product){
      if(is_array($product->getStockData())){
          $this->_addRow($product->getStockData(), $product);
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