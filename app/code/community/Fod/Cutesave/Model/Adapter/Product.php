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

    public function convert( Mage_Catalog_Model_Product $_item ) {

        // _store	_attribute_set	_type	_category	_product_websites


        $data = array();

        foreach(  $_item->getData() AS $k => $v ) {
            if ( ( is_string( $v ) || is_numeric( $v ) ) && !in_array( $k, $this->_attributeBlacklist ) ) {

                $data[ $k ] = $v;

            }
        }

        $data['_store'] = '';
        $data['_attribute_set'] = 'Default';
        $data['_type'] = 'simple';
        $data['_category'] = '';
        $data['_product_websites'] = 'base';


        print_r( $data );

        return $data;
    }

    public function getData() {
        $result = array();
        foreach( $this->_getQueue()->getItems() AS $_item ) {
            if ( $_item instanceof Mage_Catalog_Model_Product  ) {
                $result[] = $this->convert( $_item );
            }
        }

        return $result;
    }

    

}