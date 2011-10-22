<?php

class Fod_Cutesave_Model_Observer {

    public function catalogProductSaveBefore( $event ) {

        $queue = Mage::getSingleton('fod_cutesave/queue');
        /* @var $queue Fod_CuteSave_Model_Product_Queue */

        Mage::log("FOD: OBSERVER");

        if ( $queue->getEnabled() ) {

            Mage::log("FOD: OBSERVER ENABLED");
            
            $product = $event->getProduct();
            $queue->add( clone $product );

            $product->setData( $product->getOrigData() );

            //$product->reset();
            // TODO: Testen ob Produkt nun nicht gespeichert wird ;)

        }    

    }

}