<?php

namespace Ups;

use DOMDocument;
use SimpleXMLElement;
use Exception;
use stdClass;

/**
 * Address Validation API Wrapper
 *
 * @package ups
 */
class AddressValidation extends Ups
{
    const ENDPOINT = '/AV';

    /**
     * @var RequestInterface
     */
    private $request;

    /**
     * @var ResponseInterface
     * // todo make private
     */
    public $response;

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
        $this->requestOptions = array();
        parent::__construct($accessKey, $userId, $password, $useIntegration);
    }
    
    /**
     * Get Address Validation information
     *
     * @param array $requestOptions
     * @return stdClass
     * @throws Exception
     */
    public function validate($requestOptions)
    {
        if(!$this->validateRequestKeys($requestOptions)) {
            throw new Exception("Invalid Request Options for Address Validation");
        }
        
        $this->requestOptions = $requestOptions;

        $access = $this->createAccess();
        $request = $this->createRequest();

        $this->response = $this->getRequest()->request($access, $request, $this->compileEndpointUrl(self::ENDPOINT));
        $response = $this->response->getResponse();

        if (null === $response) {
            throw new Exception("Failure (0): Unknown error", 0);
        }

        if ($response instanceof SimpleXMLElement && $response->Response->ResponseStatusCode == 0) {
            throw new Exception(
                "Failure ({$response->Response->Error->ErrorSeverity}): {$response->Response->Error->ErrorDescription}",
                (int)$response->Response->Error->ErrorCode
            );
        } else {
            // TODO
            // We don't need to return data regarding the response to the user
            return $this->formatResponse($response);
        }
    }
    
    /**
     * Validate Request Keys
     *
     * @param array $requestOption
     * @return bool
     */
    private function validateRequestKeys($requestOption)
    {
        $isValid = true;
        if(array_key_exists('Address', $requestOption)) {
            $isValid = $this->validateAddress($requestOption['Address']);
        }
                
        return $isValid;
    }
    
    /**
     * Validate Address Keys
     *
     * @param array $addressOptions
     * @return bool
     */
    private function validateAddress($addressOptions)
    {
        $isValid = false;
        $keys = array_keys($addressOptions);
        $keysLen = count($keys);
        switch ($keysLen) {
            case 1:
                $isValid = $this->validateForSingleKey($keys[0]);
                break;
            case 2:
                $isValid = $this->validateForTwoKeys($addressOptions);
                break;
            case 3:
                $isValid = $this->validateForThreeKeys($addressOptions);
                break;
            case 4:
                $isValid = $this->validateForFourKeys($addressOptions);
                break;
            default:
                break;
        }
        
        return $isValid;
    }

    /**
     * Validate for Single Key
     *
     * @param string $key
     * @return bool
     */
    private function validateForSingleKey($key)
    {
        $isValid = false;
        switch ($key)
        {
            case 'City':
            case 'PostalCode':
                $isValid = true;
                break;
            default:
                break;
        }
        
        return $isValid;
    }
    
    /**
     * Validate for Two Keys
     *
     * @param array $requestOption
     * @return bool
     */
    private function validateForTwoKeys($requestOption)
    {
        $isValid = false;
        if(array_key_exists('City', $requestOption)) {
            if(array_key_exists('PostalCode', $requestOption)) {
                $isValid = true;
            }
            elseif(array_key_exists('StateProvinceCode', $requestOption)) {
                $isValid = true;
            }
        }
        elseif(array_key_exists('PostalCode', $requestOption))
        {
            if(array_key_exists('StateProvinceCode', $requestOption)) {
                $isValid = true;
            }
        }
        
        return $isValid;
    }
    
    /**
     * Validate for Three Keys
     *
     * @param array $requestOption
     * @return bool
     */
    private function validateForThreeKeys($requestOption)
    {
        $isValid = false;
        if(array_key_exists('City', $requestOption) && array_key_exists('StateProvinceCode', $requestOption)
                && array_key_exists('PostalCode', $requestOption)) {
            $isValid = true;
        }
        
        return $isValid;
    }
    
    /**
     * Validate for Four Keys
     *
     * @param array $requestOption
     * @return bool
     */
    private function validateForFourKeys($requestOption)
    {
        $isValid = false;
        if(array_key_exists('City', $requestOption) && array_key_exists('StateProvinceCode', $requestOption)
                && array_key_exists('PostalCode', $requestOption) && array_key_exists('CountryCode', $requestOption)) {
            $isValid = true;
        }
        
        return $isValid;
    }

    /**
     * Create the Address Validation request
     *
     * @return string
     */
    private function createRequest()
    {
        $xml = new DOMDocument();
        $xml->formatOutput = true;

        $avRequest = $xml->appendChild($xml->createElement("AddressValidationRequest"));
        $avRequest->setAttribute('xml:lang', 'en-US');

        $request = $avRequest->appendChild($xml->createElement("Request"));

        $node = $xml->importNode($this->createTransactionNode(), true);
        $request->appendChild($node);

        if(array_key_exists('TransactionReference', $this->requestOptions)){
            $transactionReferenceNode = $request->appendChild($xml->createElement("TransactionReference"));
            $transactionReferenceOptions = $this->requestOptions['TransactionReference'];
            $this->addOptionsToNode($transactionReferenceOptions, $transactionReferenceNode, $xml);
        }
        $request->appendChild($xml->createElement("RequestAction", "AV"));
        if(array_key_exists('RequestOption', $this->requestOptions)){
            $request->appendChild($xml->createElement("RequestOption", $this->requestOptions['RequestOption']));
        }
        
        $address = $avRequest->appendChild($xml->createElement("Address"));
        if(array_key_exists('Address', $this->requestOptions)) {
            $addressOptions = $this->requestOptions['Address'];
            $this->addOptionsToNode($addressOptions, $address, $xml);
        }

        return $xml->saveXML();
    }
    
    /**
     * Add Options To Node
     *
     * @param array $options
     * @param SimpleXMLElement $node
     * @param DOMDocument $xml
     */
    private function addOptionsToNode($options, $node, $xml)
    {
        foreach ($options as $key => $value) {
            $node->appendChild($xml->createElement($key, $value));
        }
    }
    
    /**
     * Format the response
     *
     * @param SimpleXMLElement $response
     * @return stdClass
     */
    private function formatResponse(SimpleXMLElement $response)
    {
        return $this->convertXmlObject($response);
    }
    
    /**
     * @return RequestInterface
     */
    public function getRequest()
    {
        if (null === $this->request) {
            $this->request = new Request;
        }
        return $this->request;
    }

    /**
     * @param RequestInterface $request
     * @return $this
     */
    public function setRequest(RequestInterface $request)
    {
        $this->request = $request;
        return $this;
    }

    /**
     * @return ResponseInterface
     */
    public function getResponse()
    {
        return $this->response;
    }

    /**
     * @param ResponseInterface $response
     * @return $this
     */
    public function setResponse(ResponseInterface $response)
    {
        $this->response = $response;
        return $this;
    }
}
