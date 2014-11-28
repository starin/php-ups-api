<?php

namespace Ups;

//use DOMDocument;
//use SimpleXMLElement;
use Exception;
use stdClass;
use SoapClient;
//use SoapHeader;
class RateFreight extends Ups{
    //put your code here
     const ENDPOINT = '/FreightRate';
    const WSDL_EXT = "\RateFreight\FreightRate.wsdl";
    
    /**
     * @var string
     */
    private $operation;

    /**
     * @var array
     */
    private $requestOptions;
    
    /**
     * @var SoapClient
     */
    private $soapClient;

    /**
     * @param string|null $accessKey UPS License Access Key
     * @param string|null $userId UPS User ID
     * @param string|null $password UPS User Password
     * @param bool $useIntegration Determine if we should use production or CIE URLs.
     */    
    public function __construct($accessKey = null, $userId = null, $password = null, $useIntegration = false)
    {
        parent::__construct($accessKey, $userId, $password, $useIntegration);
    }
    
    /**
     * Pickup Freight Request
     *
     * @param string $operation The operation/function name 
     * @param array $requestOptions
     * @return stdClass
     * @throws Exception
     */
    public function RateRequest($operation, $requestOptions)
    {
        $this->operation = $operation;
        $this->requestOptions = $requestOptions;
        
        $this->setSoapClient();
        $this->setLocation();
        $this->setSoapHeaders();
            
        try
        {
            $response = $this->soapCall();
            
            if (null === $response) {
                throw new Exception("Failure (0): Unknown error", 0);
            }
            
            return $response;
        }
        catch (Exception $ex)
        {
            throw $ex;
        }
    }
    
    /**
     * Set Location for Soap Client
     */
    private function setLocation()
    {
        //set endpoint url
        $this->soapClient->__setLocation($this->compileEndpointUrl(self::ENDPOINT, true));
    }

    /**
     * Set Soap Client
     */
    private function setSoapClient()
    {
        // initialize soap client
        $this->soapClient = $this->getSoapClient(self::WSDL_EXT);
    }
    
    /**
     * Set Soap Headers for Soap Client
     */
    private function setSoapHeaders()
    {
        //create soap header
        $this->soapClient->__setSoapHeaders($this->createHeader());
    }
    
    /**
     * @return mixed SOAP functions may return one, or multiple values.
     */
    private function soapCall()
    {
        $response = $this->soapClient->__soapCall($this->operation, array($this->requestOptions));
        
        return $response;
    }
    
    /**
     * Returns last SOAP request
     * @return string
     */
    public function getRequest()
    {
        return $this->soapClient->__getLastRequest();
    }

    /**
     * Returns last SOAP response
     * @return string
     */
    public function getResponse()
    {
        return $this->soapClient->__getLastResponse();
    }
    
}