<?php

namespace Ups\Entity;
Class SoapShipFrom extends Shipper{

    public function __construct() {
         /**
     * @return string
     */
    }
    
    public function getName()
    {
        return $this->name;
    }
     /**
     * @param string $requestOption
     * @return $this
     */
    public function setName($name)
    {
        $this->Name = $name;
        $this->Name = $name;
        return $this;
    } 
    

}