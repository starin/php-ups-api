<?php

namespace Ups\Entity;
class SoapShipTo extends ShipTo{
    public $Name;
    //put your code here
    private $name;
    public function getName() {
        return $this->name;  
    }
    public function setName($name) {
        $this->Name = $name;
        $this->name = $name;
    }
            
}
