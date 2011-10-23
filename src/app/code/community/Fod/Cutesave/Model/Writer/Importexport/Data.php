<?php

class Fod_Cutesave_Model_Writer_Importexport_Data extends Mage_ImportExport_Model_Mysql4_Import_Data {

    protected $_dataBunch = null;

    /**
    * Retrieve an external iterator
    *
    * @return IteratorIterator
    */
    public function getIterator ()
    {
        return new IteratorIterator($this->_stmt);
    }

    public function setDataBunch($data = array())
    {
        $this->_dataBunch = $data;
        return $this;
    }

    /**
    * Clean all bunches from table.
    *
    * @return int
    */
    public function cleanBunches()
    {
        $this->_dataBunch = null;
        return 0;
    }

    /**
    * Return behavior from import data table.
    *
    * @throws Exception
    * @return string
    */
    public function getBehavior()
    {
        return 'replace';
    }

    /**
    * Return entity type code from import data table.
    *
    * @throws Exception
    * @return string
    */
    public function getEntityTypeCode()
    {
        return 'catalog_product';
    }

    /**
    * Get next bunch of validatetd rows.
    *
    * @return array|null
    */
    public function getNextBunch()
    {
        static $_i;
        $_i++;
        if ( $_i  % 2 != 0 ) {
            return null;
        }
        return $this->_dataBunch;
    }

    /**
    * Save import rows bunch.
    *
    * @param string $entity
    * @param string $behavior
    * @param array $data
    * @return int
    */
    public function saveBunch($entity, $behavior, array $data)
    {
        return 0;
    }
}