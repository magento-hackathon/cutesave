<?php

require dirname(__FILE__).'/../../../../../Mage.php';
//require '/home/tobias/www/foocamp/app/Mage.php';
Mage::app('admin');

for($i=0; $i<= 50; $i++) {

$product = Mage::getModel('catalog/product');
$product->setStoreId(0);
$product->setWebsiteId(1);
$product->setData('sku', 'z' . $i);
$product->setData('name', 'name y' . $i);
$product->setData('description', 'Description ' . $i);
$product->setData('short_description', 'Description ' . $i);
//$product->setCategoryIds(array(10, 22));
$product->setAttributeSetId('4');
$product->setTypeId('simple');
$product->setVisibility(Mage_Catalog_Model_Product_Visibility::VISIBILITY_BOTH);
$product->setStatus(Mage_Catalog_Model_Product_Status::STATUS_ENABLED);
$product->setData('price', $i);
$product->setTaxClassId(0);

$product->setWeight(0);

   
$sizes = array('S','M','XL');
$options = array(
        'is_require' => true,
        'sort_order' => '1',
        'title' => 'Größe',
        'type' => 'drop_down',
        'values' => array()
);
foreach( $sizes AS $size ) {
    $options['values'][] = array(
        'price' => 0,
        'price_type' => 'fixed', // 'percent'
        'sku' => '',
        'sort_order' => '0',
        'title' => $size,
     );
}
$product->setProductOptions( array( 6 => $options) );
$product->setHasOptions(1);
$product->setCanSaveCustomOptions(1);



//$product->setStockData(
//    array(
//        'is_in_stock' => 1,
//         'qty' => 1111
//));

$product->save(); echo $product->getId();



exit;

Mage::getSingleton('fod_cutesave/queue')->add( $product );

    echo ".";

}

Mage::getSingleton('fod_cutesave/queue')->write();


