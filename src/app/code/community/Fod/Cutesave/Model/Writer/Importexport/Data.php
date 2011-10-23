<?php

class Fod_Cutesave_Model_Writer_Importexport_Data extends Mage_ImportExport_Model_Mysql4_Import_Data {

    protected $_dataBunch = null;

    public function setDataBunch($data = array())
    {
        $this->_dataBunch = $data;
        $this->_iterator = null;
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
        if (null === $this->_iterator) {
            $this->_iterator = $this->getIterator();
            $this->_iterator->rewind();
        }
        if ($this->_iterator->valid()) {
            $dataRow = $this->_iterator->current();
            $this->_iterator->next();
        } else {
            $this->_iterator = null;
            $dataRow = null;
        }
        return $dataRow;
    }


    /**
     * Retrieve an external iterator
     *
     * @return IteratorIterator
     */
    public function getIterator()
    {
        // TODO: generates smaller bunches
        return new ArrayIterator( array($this->_dataBunch) );
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