<?php

namespace Ups\Entity;
class SoapFreightRateRequest {
    /** @deprecated */
    public $RequestOption;
    public $ShipFrom;
    public $ShipTo;
    public  $PaymentInformation;
    
    /**
     * @var string
     */
    private $request;
    private $requestOption;
    private $shipFrom;
    private $shipTo;
    private  $payerTo;
    private  $paymentInformation;
    public function __construct() {
        $this->setShipFrom(new SoapShipFrom());
        $this->setShipTo(new SoapShipTo());
        $this->setPaymentInformation(new PaymentInformation());
        $this->request = array();
    }
    /**
     * @return string
     */
    public function getRequestOption()
    {
        return $this->requestOption;
    }
     /**
     * @param string $requestOption
     * @return $this
     */
    public function setRequestOption($requestOption)
    {
        $this->RequestOption = $requestOption;
        $this->requestOption = $requestOption;
        return $this;
    }
   
   
      /**
     * @return string
     */
    public function getShipFrom()
    {
        return $this->shipFrom;
    }

    /**
     * @param string $shipFrom
     * @return $this
     */
    public function setShipFrom($shipFrom)
    {
        $this->ShipFrom = $shipFrom;
        $this->shipFrom = $shipFrom;
        return $this;
    }
    
          /**
     * @return string
     */
    public function getShipTo()
    {
        return $this->shipTo;
    }

    /**
     * @param string $shipTo
     * @return $this
     */
    public function setShipTo($shipTo)
    {
        $this->ShipTo = $shipTo;
        $this->shipTo = $shipTo;
        return $this;
    }
       
          /**
     * @return string
     */
    public function getPaymentInformation()
    {
        return $this->paymentInformation;
    }

    /**
     * @param string $paymentInformation
     * @return $this
     */
    public function setPaymentInformation(PaymentInformation $paymentInformation)
    {
        $this->paymentInformation = $paymentInformation;
        $this->PaymentInformation = $paymentInformation;
        return $this;
    }
}
