<?php

namespace Ups;

use DOMDocument;
use SimpleXMLElement;
use Exception;
use stdClass;

/**
 * Address Validation Street Level API Wrapper
 * Only for United States
 *
 * @package ups
 */
class AddressValidationStreetLevel extends Ups
{
    const ENDPOINT = '/XAV';

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
     * @param array $requestOption
     * @return stdClass
     * @throws Exception
     */
    public function validate($requestOptions)
    {
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
     * Create the Address Validation request
     *
     * @return string
     */
    private function createRequest()
    {
        $xml = new DOMDocument();
        $xml->formatOutput = true;

        $xavRequest = $xml->appendChild($xml->createElement("AddressValidationRequest"));
        $xavRequest->setAttribute('xml:lang', 'en-US');

        $request = $xavRequest->appendChild($xml->createElement("Request"));

        $node = $xml->importNode($this->createTransactionNode(), true);
        $request->appendChild($node);

        if(array_key_exists('TransactionReference', $this->requestOptions)){
            $transactionReferenceNode = $request->appendChild($xml->createElement("TransactionReference"));
            $transactionReferenceOptions = $this->requestOptions['TransactionReference'];
            $this->addOptionsToNode($transactionReferenceOptions, $transactionReferenceNode, $xml);
        }
        $request->appendChild($xml->createElement("RequestAction", "XAV"));
        if(array_key_exists('RequestOption', $this->requestOptions)){
            $request->appendChild($xml->createElement("RequestOption", $this->requestOptions['RequestOption']));
        }
        
        if(array_key_exists('RegionalRequestIndicator', $this->requestOptions)) {
            $xavRequest->appendChild($xml->createElement("RegionalRequestIndicator", $this->requestOptions['RegionalRequestIndicator']));
        }
        
        if(array_key_exists('MaximumListSize', $this->requestOptions)) {
            $xavRequest->appendChild($xml->createElement("MaximumListSize", $this->requestOptions['MaximumListSize']));
        }
        
        if(array_key_exists('AddressKeyFormat', $this->requestOptions)) {
            $address = $xavRequest->appendChild($xml->createElement("AddressKeyFormat"));
            $addressOptions = $this->requestOptions['AddressKeyFormat'];
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
