<?php
namespace Ups\Entity;
class Payer {

    private $name;
    private $address;

    //put your code here
    public function __construct() {
        $this->setAddress(new Address()) ;
    }

    /**
     * @return string
     */
    public function getName() {
        return $this->name;
    }

    /**
     * @param string $Name
     * @return $this
     */
    public function setName($name) {
        $this->Name = $name;
        $this->name = $name;
        return $this;
    }

    /**
     * @return string
     */
    public function getAddress() {
        return $this->address;
    }

    /**
     * @param string $address
     * @return $this
     */
    public function setAddress(Address $address) {
        $this->Address = $address;
        $this->address = $address;
        return $this;
    }

}
