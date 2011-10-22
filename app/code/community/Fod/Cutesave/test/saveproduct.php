<?php

require dirname(__FILE__).'/../../../../../Mage.php';
Mage::app('admin');

for($i=0; $i<= 5000; $i++) {

    $product = Mage::getModel('catalog/product');
    $product->setStoreId(0);
    $product->setWebsiteId(1);
    $product->setData('sku', 'z' . $i);
    $product->setData('name', 'name y' . $i);
    $product->setData('description', 'Description ' . $i);
    $product->setData('short_description', 'Description ' . $i);
    $product->setAttributeSetId('4');
    $product->setTypeId('simple');
    $product->setVisibility(Mage_Catalog_Model_Product_Visibility::VISIBILITY_BOTH);
    $product->setStatus(Mage_Catalog_Model_Product_Status::STATUS_ENABLED);
    $product->setData('price', $i);
    $product->setTaxClassId(0);

    $product->setWeight(0);


    $product->setStockData(
        array(
            'is_in_stock' => 1,
             'qty' => 1
    ));
    
    // $product->save();
    Mage::getSingleton('fod_cutesave/queue')->add( $product );

    echo ".";

}

Mage::getSingleton('fod_cutesave/queue')->write();


