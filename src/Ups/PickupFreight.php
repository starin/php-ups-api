<?php

namespace Ups;

//use DOMDocument;
use SimpleXMLElement;
use Exception;
use stdClass;
use SoapClient;
use SoapHeader;

class PickupFreight extends Ups
{
    const ENDPOINT = '/FreightPickup';
    const WSDL_EXT = "\PickupFreight\FreightPickup.wsdl";
    
//    private $endpointurl = "https://wwwcie.ups.com/webservices/FreightPickup";
//    const WSDL_DIRECTORY = '\wsdl';
    
//    private $mode = array(
//        'soap_version' => 'SOAP_1_1',  // use soap 1.1 client
//        'trace' => 1
//    );
    
    /**
     * @var string
     */
    private $operation;

    /**
     * @var array
     */
    private $requestOptions;
    
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
    
    public function pickupRequest($operation, $requestOptions)
    {
        $this->operation = $operation;
        $this->requestOptions = $requestOptions;
        
        // initialize soap client
        $client = $this->getSoapClient(self::WSDL_EXT);

        //set endpoint url
        $client->__setLocation($this->compileEndpointUrl(self::ENDPOINT, true));
        
        //create soap header
        $client->__setSoapHeaders($this->createHeader());
        
        $resp = $client->__soapCall($this->operation, array($this->requestOptions));
        
//        $response = $client->__getLastResponse();
        
        return $this->formatResponse($resp);
    }

    /**
     * Format the response
     *
     * @param object $response
     * @return object
     */
    private function formatResponse($response)
    {
        return json_decode(json_encode($response));
    }
}
