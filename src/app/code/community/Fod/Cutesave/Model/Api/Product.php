<?php

/*
 * based on Mage_Catalog_Model_Product_Api, replaced $product->save() with fod_cutesave queue
 */

class Fod_Cutesave_Model_Api_Product extends Mage_Catalog_Model_Product_Api {

    public function update($productId, $productData, $store = null, $identifierType = null) {
        $product = $this->_getProduct($productId, $store, $identifierType);

        $this->_prepareDataForSave($product, $productData);

        try {
            if (is_array($errors = $product->validate())) {
                $strErrors = array();
                foreach ($errors as $code => $error) {
                    if ($error === true) {
                        $error = Mage::helper('catalog')->__('Value for "%s" is invalid.', $code);
                    } else {
                        $error = Mage::helper('catalog')->__('Value for "%s" is invalid: %s', $code, $error);
                    }
                    $strErrors[] = $error;
                }
                $this->_fault('data_invalid', implode("\n", $strErrors));
            }

            Mage::getSingleton('fod_cutesave/queue')->add($product);
            Mage::getSingleton('fod_cutesave/queue')->write();
        } catch (Mage_Core_Exception $e) {
            $this->_fault('data_invalid', $e->getMessage());
        }

        return true;
    }

    public function create($type, $set, $sku, $productData, $store = null) {
        if (!$type || !$set || !$sku) {
            $this->_fault('data_invalid');
        }

        $this->_checkProductTypeExists($type);
        $this->_checkProductAttributeSet($set);

        /** @var $product Mage_Catalog_Model_Product */
        $product = Mage::getModel('catalog/product');
        $product->setStoreId($this->_getStoreId($store))
                ->setAttributeSetId($set)
                ->setTypeId($type)
                ->setSku($sku);

        $this->_prepareDataForSave($product, $productData);

        try {
            if (is_array($errors = $product->validate())) {
                $strErrors = array();
                foreach ($errors as $code => $error) {
                    if ($error === true) {
                        $error = Mage::helper('catalog')->__('Attribute "%s" is invalid.', $code);
                    }
                    $strErrors[] = $error;
                }
                $this->_fault('data_invalid', implode("\n", $strErrors));
            }

            Mage::getSingleton('fod_cutesave/queue')->add($product);
            Mage::getSingleton('fod_cutesave/queue')->write();
        } catch (Mage_Core_Exception $e) {
            $this->_fault('data_invalid', $e->getMessage());
        }

        return $product->getId();
    }

    protected function _prepareDataForSave($product, $productData) {

        foreach ($product->getTypeInstance(true)->getEditableAttributes($product) as $attribute) {
            if ($this->_isAllowedAttribute($attribute)) {
                if (isset($productData[$attribute->getAttributeCode()])) {
                    $product->setData(
                            $attribute->getAttributeCode(), $productData[$attribute->getAttributeCode()]
                    );
                } elseif (isset($productData['additional_attributes']['single_data'][$attribute->getAttributeCode()])) {
                    $product->setData(
                            $attribute->getAttributeCode(), $productData['additional_attributes']['single_data'][$attribute->getAttributeCode()]
                    );
                } elseif (isset($productData['additional_attributes']['multi_data'][$attribute->getAttributeCode()])) {
                    $product->setData(
                            $attribute->getAttributeCode(), $productData['additional_attributes']['multi_data'][$attribute->getAttributeCode()]
                    );
                }
            }
        }
    }

    public function multiwrite($data) {
        foreach($data as $productData) {
            $product = Mage::getModel('catalog/product');
            /** @var Mage_Catalog_Model_Product $product */
            
            $this->_prepareDataForSave($product, $productData);
            
            Mage::getSingleton('fod_cutesave/queue')->add($product);
        }
        
        Mage::getSingleton('fod_cutesave/queue')->write();
    }
}
