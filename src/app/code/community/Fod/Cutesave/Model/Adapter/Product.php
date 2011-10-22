<?php

class Fod_Cutesave_Model_Adapter_Product extends Mage_ImportExport_Model_Import_Entity_Product {

    protected $_attributeBlacklist = array(
        'entity_type_id',
        'attribute_set_id',
        'options_container',
        'msrp_enabled',
        'msrp_display_actual_price_type'
    );

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

    public function convert( Mage_Catalog_Model_Product $_item ) {

        $rows = array();

        $data = array();

        foreach(  $_item->getData() AS $k => $v ) {
            if ( ( is_string( $v ) || is_numeric( $v ) ) && !in_array( $k, $this->_attributeBlacklist ) ) {

                $data[ $k ] = $v;

            }
        }

        $data['_store'] = $_item->getStoreIds();
        $data['_attribute_set'] = $_item->getAttributeSetId();
        $data['_type'] = $_item->getTypeId();
        $data['_category'] = $_item->getCategoryIds();
        $data['_product_websites'] = $_item->getWebsiteIds();

        $rows[] = $data;

        // TODO: add some magic containing images and options
        
        return $rows;
    }


    public function getData() {
        $result = array();
        foreach( $this->_getQueue()->getItems() AS $_item ) {
            if ( $_item instanceof Mage_Catalog_Model_Product  ) {
                foreach( $this->convert( $_item ) AS $_row ) {
                    $result[] = $_row;
                }
            }
        }
        return $result;
    }

    

}