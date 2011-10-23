<?php

$client = new SoapClient('http://mage.local/magento-1.6.1.0/api/soap?wsdl');

try {
    $session = $client->login('foo', 'barbar');
} catch (Exception $e) {
    echo $e->getMessage() . "\n";
    exit;
}

$data = array();

for ($i = 0; $i < 1000; $i++) {
    $productData = array(
        'name' => '2name' . $i,
        'website_id' => 1,
        'store_id' => 0,
        'short_description' => 'short description',
        'description' => 'description',
        'status' => 1,
        'weight' => '0',
        'tax_class_id' => '4',
        'categories' => array(3),
        'price' => '12.05',
        'visibility' => 4,
        'attribute_set_id' => 4,
        'type_id' => 'simple',
        'sku'   => 's' . $i,
        'type' => 'simple',
    );    
    $data[] = $productData;
}

try {
    print_r($data);
    $client->call($session, 'cutesave_product.multiwrite', array($data));
} catch (Exception $e) {
    echo $e->getMessage() . "\n";
    exit;
}

$client->endSession($session);
