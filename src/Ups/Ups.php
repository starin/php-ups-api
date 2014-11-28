<?php
namespace Ups;

use DOMDocument;
use SimpleXMLElement;
use Exception;
use stdClass;
use SoapClient;
use SoapHeader;

abstract class Ups
{
    const WSDL_DIRECTORY = '\wsdl';
    const PRODUCTION_BASE_URL = 'https://onlinetools.ups.com/ups.app/xml';
    const INTEGRATION_BASE_URL = 'https://wwwcie.ups.com/ups.app/xml';

    /**
     * @var string
     */
    protected $accessKey;

    /**
     * @var string
     */
    protected $userId;

    /**
     * @var string
     */
    protected $password;

    /**
     * @var string
     * @deprecated
     */
    protected $productionBaseUrl = 'https://onlinetools.ups.com/ups.app/xml';

    /**
     * @var string
     * @deprecated
     */
    protected $integrationBaseUrl = 'https://wwwcie.ups.com/ups.app/xml';
    
    /**
     * @var string
     */
    protected $productionBaseWebUrl = 'https://onlinetools.ups.com/webservices';

    /**
     * @var string
     */
    protected $integrationBaseWebUrl = 'https://wwwcie.ups.com/webservices';
    
    /**
     * @var string
     */
    protected $soapNamespace = "http://www.ups.com/XMLSchema/XOLTWS/UPSS/v1.0";
    
    /**
     * @var array
     */
    protected $mode;

    /**
     * @var bool
     */
    protected $useIntegration = false;

    /**
     * @var string
     */
    protected $context;

    /**
     * @deprecated
     */
    public $response;

    /**
     * Constructor
     *
     * @param string|null $accessKey UPS License Access Key
     * @param string|null $userId UPS User ID
     * @param string|null $password UPS User Password
     * @param bool $useIntegration Determine if we should use production or CIE URLs.
     */
    public function __construct($accessKey = null, $userId = null, $password = null, $useIntegration = false)
    {
        $this->accessKey = $accessKey;
        $this->userId = $userId;
        $this->password = $password;
        $this->useIntegration = $useIntegration;
        
        $this->mode = array(
            'soap_version' => 'SOAP_1_1',  // use soap 1.1 client
            'trace' => 1
        );
    }

    /**
     * Sets the transaction / context value
     *
     * @param string $context The transaction "guidlikesubstance" value
     */
    public function setContext($context)
    {
        $this->context = $context;
    }

    /**
     * Format a Unix timestamp or a date time with a Y-m-d H:i:s format into a YYYYMMDDHHmmss format required by UPS.
     *
     * @param string
     * @return string
     */
    public function formatDateTime($timestamp)
    {
        if (!is_numeric($timestamp)) {
            $timestamp = strtotime($timestamp);
        }

        return date('YmdHis', $timestamp);
    }

    /**
     * Create the access request
     *
     * @return string
     */
    protected function createAccess()
    {
        $xml = new DOMDocument();
        $xml->formatOutput = true;

        // Create the AccessRequest element
        $accessRequest = $xml->appendChild($xml->createElement("AccessRequest"));
        $accessRequest->setAttribute('xml:lang', 'en-US');

        $accessRequest->appendChild($xml->createElement("AccessLicenseNumber", $this->accessKey));
        $accessRequest->appendChild($xml->createElement("UserId", $this->userId));
        $accessRequest->appendChild($xml->createElement("Password", $this->password));

        return $xml->saveXML();
    }
    
    /**
     * Create the Soap Client for web services
     *
     * @param string $ext
     * @return SoapClient
     */
    protected function getSoapClient($ext)
    {
        $client = new SoapClient($this->getWSDLFilePath($ext), $this->mode);
        
        return $client;
    }
    
    /**
     * Create the Header for web services
     *
     * @return SoapHeader
     */
    protected function createHeader()
    {
        $usernameToken['Username'] = $this->userId;
        $usernameToken['Password'] = $this->password;
        $serviceAccessLicense['AccessLicenseNumber'] = $this->accessKey;
        
        $upss['UsernameToken'] = $usernameToken;
        $upss['ServiceAccessToken'] = $serviceAccessLicense;

        $header = new SoapHeader($this->soapNamespace, 'UPSSecurity', $upss);
        
        return $header;
    }

    /**
     * Creates the TransactionReference node for a request
     *
     * @return DomDocument
     */
    protected function createTransactionNode()
    {
        $xml = new DOMDocument;
        $xml->formatOutput = true;

        $trxRef = $xml->appendChild($xml->createElement('TransactionReference'));

        if (null !== $this->context) {
            $trxRef->appendChild($xml->createElement('CustomerContext', $this->context));
        }

        return $trxRef->cloneNode(true);
    }

    /**
     * Send request to UPS
     *
     * @param string $access The access request xml
     * @param string $request The request xml
     * @param string $endpointurl The UPS API Endpoint URL
     * @return SimpleXMLElement
     * @throws Exception
     * @deprecated Untestable
     */
    protected function request($access, $request, $endpointurl)
    {
        $requestInstance = new Request;
        $response = $requestInstance->request($access, $request, $endpointurl);
        if ($response->getResponse() instanceof SimpleXMLElement) {
            $this->response = $response->getResponse();
            return $response->getResponse();
        }

        throw new Exception("Failure: Response is invalid.");
    }

    /**
     * Convert XMLSimpleObject to stdClass object
     *
     * @param SimpleXMLElement $xmlObject
     * @return stdClass
     */
    protected function convertXmlObject(SimpleXMLElement $xmlObject)
    {
        return json_decode(json_encode($xmlObject));
    }

    /**
     * Compiles the final endpoint URL for the request.
     *
     * @param string $segment The URL segment to build in to the endpoint
     * @param bool $isWebService The URL segment to build in to the endpoint of web service or not
     * @return string
     */
    protected function compileEndpointUrl($segment, $isWebService = false)
    {
        if($isWebService) {
            $base = ($this->useIntegration ? $this->integrationBaseWebUrl : $this->productionBaseWebUrl);
        } else {
            $base = ($this->useIntegration ? $this->integrationBaseUrl : $this->productionBaseUrl);
        }
        
        return $base . $segment;
    }
    
    /**
     * Get full Path of .wsdl file
     *
     * @param string $ext
     * @return string
     */
    protected function getWSDLFilePath($ext)
    {
        return realpath(__DIR__ . self::WSDL_DIRECTORY . $ext);
    }
}
