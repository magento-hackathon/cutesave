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

        /* @var $_item  Mage_Catalog_Model_Product */

        // _store	_attribute_set	_type	_category	_product_websites

        $data = array();

        foreach( $_item->getData() AS $k => $v ) {

            $attribute = $this->getAttribute( $k );
            if ( $attribute->getId() ) {

                die( get_class( $attribute ) );

                //switch( $attribute-> )

            }

        }

        $data['_store'] = $_item->getStoreIds();
        $data['_attribute_set'] = $_item->getAttributeSetId();
        $data['_type'] = $_item->getTypeId();
        $data['_category'] = $_item->getCategoryIds();
        $data['_product_websites'] = $_item->getWebsiteIds();

        print_( $data );

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