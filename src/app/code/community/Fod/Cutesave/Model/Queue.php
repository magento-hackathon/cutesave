<?php

class Fod_Cutesave_Model_Queue {

    protected $_items = Array();

    protected $_writer;
    /* @var $_writer Fod_CuteSave_Model_Writer_Abstract */

    protected $_adapter;
    /* @var $_writer Fod_CuteSave_Model_Adapter_Abstract */

    protected $_enabled = true;

    public function __construct() {
        $this->setWriter( Mage::getModel('fod_cutesave/writer_importexport') );
        $this->setAdapter( Mage::getModel('fod_cutesave/adapter_product') );
    }

    public function setWriter( Fod_Cutesave_Model_Writer_Importexport $writer  ) {
        $this->_writer = $writer;
        return $this;
    }

    /**
     * @return Fod_CuteSave_Model_Writer_Importexport
     */
    public function getWriter() {
        return $this->_writer;
    }

    public function setAdapter( Fod_Cutesave_Model_Adapter_Product $adapter  ) {
        $this->_adapter = $adapter;
        return $this;
    }

    /**
     * @return Fod_CuteSave_Model_Writer_Importexport
     */
    public function getAdapter() {
        return $this->_adapter;
    }

    public function add( /*Mage_Eav_Model_Entity_Interface*/ $item  ) {
        Mage::log('FOD:ADD' . $item->getId() );
        $this->_items[] = $item;
        return $this;
    }

    public function getItems() {
        return $this->_items;
    }

    public function setEnabled( $bool = true ) {
        $this->_enabled = $bool;
        return $this;
    }

    public function getEnabled() {
        return $this->_enabled;
    }

    public function write() {
        $data = $this->getAdapter()->getData();
        $this->getWriter()->saveItems( $data );
    }

}