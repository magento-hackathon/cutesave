<?php

class Fod_Cutesave_Model_Writer_Importexport extends Mage_ImportExport_Model_Import_Entity_Product {

    /**
    * Constructor.
    *
    * @return void
    */
    public function __construct()
    {
        parent::__construct();
        $entityType = Mage::getSingleton('eav/config')->getEntityType($this->getEntityTypeCode());
        $this->_entityTypeId = $entityType->getEntityTypeId();
        $this->_dataSourceModel = self::getDataSourceModel();
        $this->_connection = Mage::getSingleton('core/resource')->getConnection('write');
    }
    
    
    public function saveItems($data)
    {
        $this->_dataCount = count($data);
        $this->_dataSourceModel->setDataBunch($data);
        return $this->_importData();
    }
    
   
    /**
    * Validate data rows and save bunches to DB.
    *
    * @return Mage_ImportExport_Model_Import_Entity_Abstract
    */
    protected function _saveValidatedBunches()
    {
        return $this;
    }
 
    /**
    * data source model getter.
    *
    * @static
    * @return Flagbit_Mip_Model_Resource_Importexport_Import_Data
    */
    public static function getDataSourceModel()
    {
        return Mage::getSingleton('fod_cutesave/writer_importexport_data');
    }

    /**
    * Validate data row.
    *
    * @param array $rowData
    * @param int $rowNum
    * @return boolean
    */
    public function validateRow(array $rowData, $rowNum)
    {
        $result = parent::validateRow($rowData, $rowNum);
        $this->_currentItem = $rowData;
 
        return $result;
    }
    
    /**
    * Add error with corresponding current data source row number.
    *
    * @param string $errorCode Error code or simply column name
    * @param int $errorRowNum Row number.
    * @param string $colName OPTIONAL Column name.
    * @return Mage_ImportExport_Model_Import_Adapter_Abstract
    */
    public function addRowError($errorCode, $errorRowNum, $colName = null)
    {
        $sku = isset($this->_currentItem[self::COL_SKU]) ? $this->_currentItem[self::COL_SKU] : 'unknown';
        
        Mage::log(' Product ('.$sku.') Import Error: '.$errorCode.' '.$colName);
        return parent::addRowError($errorCode, $errorRowNum, $colName);
    }
    

}