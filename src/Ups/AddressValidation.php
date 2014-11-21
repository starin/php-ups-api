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
     * @var string
     */
    private $requestOption;
    
    /**
     * @param string|null $accessKey UPS License Access Key
     * @param string|null $userId UPS User ID
     * @param string|null $password UPS User Password
     * @param bool $useIntegration Determine if we should use production or CIE URLs.
     */    
    public function __construct($accessKey = null, $userId = null, $password = null, $useIntegration = false)
    {
        $this->requestOption = array();
        parent::__construct($accessKey, $userId, $password, $useIntegration);
    }
    
    /**
     * Get Address Validation information
     *
     * @param array $requestOption Optional processing. For Mail Innovations the only valid options are Last Activity and All activity.
     * @return stdClass
     * @throws Exception
     */
    public function validate($requestOption)
    {
        if(!$this->validateRequestKeys($requestOption)) {
            throw new Exception("Invalid Request Options for Address Validation");
        }
        
        $this->requestOption = $requestOption;

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
        $isValid = false;
        $keys = array_keys($requestOption);
        $keysLen = count($keys);
        switch ($keysLen) {
            case 1:
                $isValid = $this->validateForSingleKey($keys[0]);
                break;
            case 2:
                $isValid = $this->validateForTwoKeys($requestOption);
                break;
            case 3:
                $isValid = $this->validateForThreeKeys($requestOption);
                break;
            case 4:
                $isValid = $this->validateForFourKeys($requestOption);
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

        $request->appendChild($xml->createElement("RequestAction", "AV"));

        if (null !== $this->requestOption) {
            $address = $avRequest->appendChild($xml->createElement("Address"));
            if(array_key_exists('City', $this->requestOption)) {
                $address->appendChild($xml->createElement("City", $this->requestOption['City']));
            }
            if(array_key_exists('StateProvinceCode', $this->requestOption)) {
                $address->appendChild($xml->createElement("StateProvinceCode", $this->requestOption['StateProvinceCode']));
            }
            if(array_key_exists('CountryCode', $this->requestOption)) {
                $address->appendChild($xml->createElement("CountryCode", $this->requestOption['CountryCode']));
            }
            if(array_key_exists('PostalCode', $this->requestOption)) {
                $address->appendChild($xml->createElement("PostalCode", $this->requestOption['PostalCode']));
            }
        }

        return $xml->saveXML();
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
