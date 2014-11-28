<?php

namespace Ups\Entity;
class PaymentInformation {
    public $Payer;
    public $ShipmentBillingOption;
    
    
    
    
    
    private $payer;
    private $shipmentBillingOption;
    public function __construct() {
        $this->setPayer(new Payer());
    }
      /**
     * @return string
     */
    public function getShipmentBillingOption() {
        return $this->shipmentBillingOption;
    }

    /**
     * @param stdClass $shipmentBillingOption
     * @return $this
     */
    public function setshipmentBillingOption($shipmentBillingOption) {
        
        $this->shipmentBillingOption = $shipmentBillingOption;
        $this->ShipmentBillingOption = $shipmentBillingOption;
        return $this;
    }

    /**
     * @return string
     */
    public function getPayer() {
        return $this->payer;
    }

    /**
     * @param string $payer
     * @return $this
     */
    public function setPayer(Payer $payer) {
        $this->payer = $payer;
        $this->Payer = $payer;
        return $this;
    } 
}
