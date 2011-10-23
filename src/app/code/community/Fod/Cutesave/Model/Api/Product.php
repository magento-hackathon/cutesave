<?php

class Fod_Cutesave_Model_Api_Product extends Mage_Catalog_Model_Product_Api {
    
    public function update($productId, $productData, $store = null, $identifierType = null)
    {
        $product = Mage::getModel('catalog/product');
        
        foreach($productData as $k => $v) {
            $product->setData($k, $v);
        }

        Mage::getSingleton('fod_cutesave/queue')->add($product);
        Mage::getSingleton('fod_cutesave/queue')->write();

        return true;
    }
    
    public function create($type, $set, $sku, $productData, $store = null)
    {
//        if (!$type || !$set || !$sku) {
//            $this->_fault('data_invalid');
//        }
//
//        $this->_checkProductTypeExists($type);
//        $this->_checkProductAttributeSet($set);

        /** @var $product Mage_Catalog_Model_Product */
        $product = Mage::getModel('catalog/product');
        $product->setStoreId($this->_getStoreId($store))
            ->setAttributeSetId($set)
            ->setTypeId($type)
            ->setSku($sku);
        
        foreach($productData as $k => $v) {
            $product->setData($k, $v);
        }

        //$this->_prepareDataForSave($product, $productData);

        try {
            /**
             * @todo implement full validation process with errors returning which are ignoring now
             * @todo see Mage_Catalog_Model_Product::validate()
             */
            if (is_array($errors = $product->validate())) {
                $strErrors = array();
                foreach($errors as $code => $error) {
                    if ($error === true) {
                        $error = Mage::helper('catalog')->__('Attribute "%s" is invalid.', $code);
                    }
                    $strErrors[] = $error;
                }
                $this->_fault('data_invalid', implode("\n", $strErrors));
            }

            //$product->save();
            
//             $product->setStockData(array(
//                    'is_in_stock' => 1,
//                    'qty' => 1,
//                ));

            Mage::getSingleton('fod_cutesave/queue')->add($product);
            Mage::getSingleton('fod_cutesave/queue')->write();
        } catch (Mage_Core_Exception $e) {
            $this->_fault('data_invalid', $e->getMessage());
        }

        return $product->getId();
    }
    
    protected function _prepareDataForSave($product, $productData)
    {
        
        foreach ($product->getTypeInstance(true)->getEditableAttributes($product) as $attribute) {
            if ($this->_isAllowedAttribute($attribute)) {
                if (isset($productData[$attribute->getAttributeCode()])) {
                    $product->setData(
                        $attribute->getAttributeCode(),
                        $productData[$attribute->getAttributeCode()]
                    );
                } elseif (isset($productData['additional_attributes']['single_data'][$attribute->getAttributeCode()])) {
                    $product->setData(
                        $attribute->getAttributeCode(),
                        $productData['additional_attributes']['single_data'][$attribute->getAttributeCode()]
                    );
                } elseif (isset($productData['additional_attributes']['multi_data'][$attribute->getAttributeCode()])) {
                    $product->setData(
                        $attribute->getAttributeCode(),
                        $productData['additional_attributes']['multi_data'][$attribute->getAttributeCode()]
                    );
                }
            }
        }

//        if (isset($productData['categories']) && is_array($productData['categories'])) {
//            $product->setCategoryIds($productData['categories']);
//        }
        /*
        if (isset($productData['stock_data']) && is_array($productData['stock_data'])) {
            $product->setStockData($productData['stock_data']);
        } else {
            $product->setStockData(array('use_config_manage_stock' => 0));
        }
        */
//        if (isset($productData['tier_price']) && is_array($productData['tier_price'])) {
//             $tierPrices = Mage::getModel('catalog/product_attribute_tierprice_api')
//                 ->prepareTierPrices($product, $productData['tier_price']);
//             $product->setData(Mage_Catalog_Model_Product_Attribute_Tierprice_Api::ATTRIBUTE_CODE, $tierPrices);
//        }
    }
}
