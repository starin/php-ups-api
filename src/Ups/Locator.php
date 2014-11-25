<?php

namespace Ups;

use DOMDocument;
use SimpleXMLElement;
use Exception;
use stdClass;

class Locator extends Ups {

    const ENDPOINT = '/Locator';

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
    private $requestLocator;

    /**
     * @param string|null $accessKey UPS License Access Key
     * @param string|null $userId UPS User ID
     * @param string|null $password UPS User Password
     * @param bool $useIntegration Determine if we should use production or CIE URLs.
     */
    public function __construct($accessKey = null, $userId = null, $password = null, $useIntegration = FALSE) {
//        $this->requestOption = array();
        parent::__construct($accessKey, $userId, $password, $useIntegration);
        $this->requestLocator = NULL;
        $this->OriginAddress = array();
        $this->transactionReference = array();
    }

    public function locate($requestLocate) {
        $this->requestLocator = $requestLocate;
//        var_dump($this->requestLocator);

        $access = $this->createAccess();
        $request = $this->createRequest();

        $this->response = $this->getRequest()->request($access, $request, $this->compileEndpointUrl(self::ENDPOINT));
        $response = $this->response->getResponse();

        if (null === $response) {
            throw new Exception("Failure (0): Unknown error", 0);
        }

        if ($response instanceof SimpleXMLElement && $response->Response->ResponseStatusCode == 0) {
            throw new Exception(
            "Failure ({$response->Response->Error->ErrorSeverity}): {$response->Response->Error->ErrorDescription}", (int) $response->Response->Error->ErrorCode
            );
        } else {
            return $this->formatResponse($response);
        }
    }

    public function createRequest() {
        $xml = new DOMDocument();
        $xml->formatOutput = true;
        $loc_request = $xml->appendChild($xml->createElement("LocatorRequest"));
        /*         * *
         * Request
         */
        $request = $loc_request->appendChild($xml->createElement("Request"));
        $request->appendChild($xml->createElement("RequestAction", "Locator"));
        //createoption
        if (array_key_exists("Request", $this->requestLocator)) {
            if (array_key_exists("RequestOption", $this->requestLocator["Request"])) {
                $request->appendChild($xml->createElement("RequestOption", $this->requestLocator["Request"]['RequestOption']));
            }
            //TransactionReference
            if (array_key_exists("TransactionReference", $this->requestLocator["Request"])) {

                $request->appendChild($this->addOptionsToNode($this->requestLocator["Request"]["TransactionReference"], $xml->createElement("TransactionReference"), $xml));
            }
        }


//        $request->appendChild($node);
        /**
         * origin address node
         */
        if (array_key_exists("OriginAddress", $this->requestLocator)) {
            $originAddress = $loc_request->appendChild($xml->createElement("OriginAddress"));
            //LandmarkCode
            if (array_key_exists("LandmarkCode", $this->requestLocator["OriginAddress"])) {
                $originAddress->appendChild($xml->createElement("LandmarkCode", $this->requestLocator["OriginAddress"]["LandmarkCode"]));
            }

            //Geocode
            if (array_key_exists("Geocode", $this->requestLocator["OriginAddress"])) {
//         $originAddress->appendChild($xml->createElement("Geocode"));
                $originAddress->appendChild($this->addOptionsToNode($this->requestLocator["OriginAddress"]["Geocode"], $xml->createElement("Geocode"), $xml));
            }
            //phonenumber
            $phonenumber = $xml->createElement("PhoneNumber");
            $StructuredPhoneNumber = $phonenumber->appendChild($xml->createElement("StructuredPhoneNumber"));
            if (array_key_exists("PhoneNumber", $this->requestLocator["OriginAddress"])) {
                $StructuredPhoneNumber = $this->addOptionsToNode($this->requestLocator["OriginAddress"]["PhoneNumber"], $StructuredPhoneNumber, $xml);
            }

            $phonenumber->appendChild($StructuredPhoneNumber);
            $originAddress->appendChild($phonenumber);
            if (array_key_exists("AddressKeyFormat", $this->requestLocator["OriginAddress"])) {
                $AddressKeyFormat = $xml->createElement("AddressKeyFormat");
                $originAddress->appendChild($this->addOptionsToNode($this->requestLocator["OriginAddress"]["AddressKeyFormat"], $AddressKeyFormat, $xml));
            }

            if (array_key_exists("MaximumListSize", $this->requestLocator["OriginAddress"])) {
                $originAddress->appendChild($xml->createElement("MaximumListSize", $this->requestLocator["OriginAddress"]["MaximumListSize"]));
            }
        }

        /*         * *
         * Translate
         */

        if (array_key_exists("Translate", $this->requestLocator)) {
            $loc_request->appendChild($this->addOptionsToNode($this->requestLocator["Translate"], $xml->createElement("Translate"), $xml));
        }

        /*         * *
         * UnitOfMeasurement
         */
        if (array_key_exists("UnitOfMeasurement", $this->requestLocator)) {
            $loc_request->appendChild($this->addOptionsToNode($this->requestLocator["UnitOfMeasurement"], $xml->createElement("UnitOfMeasurement"), $xml));
        }
        /**
         * LocationID
         */
        if (array_key_exists("LocationID", $this->requestLocator)) {
            $loc_request->appendChild($xml->createElement("LocationID", $this->requestLocator["LocationID"]));
        }

        /**
         * LocationSearchCriteria
         */
        if (array_key_exists("LocationSearchCriteria", $this->requestLocator)) {
            $LocationSearchCriteria = $loc_request->appendChild($xml->createElement("LocationSearchCriteria"));


//SearchOption
            if (array_key_exists("SearchOption", $this->requestLocator["LocationSearchCriteria"])) {
                $SearchOption = $LocationSearchCriteria->appendChild($xml->createElement("SearchOption"));
                //OptionType
                if (array_key_exists("OptionType", $this->requestLocator["LocationSearchCriteria"]["SearchOption"])) {



                    $optiontype = $SearchOption->appendChild($xml->createElement("OptionType"));
                    //code
                    if (array_key_exists("Code", $this->requestLocator["LocationSearchCriteria"]["SearchOption"]["OptionType"])) {
                        $optiontype->appendChild($xml->createElement("Code", $this->requestLocator["LocationSearchCriteria"]["SearchOption"]["OptionType"]["code"]));
                    }
                }
                //CodeType
                if (array_key_exists("OptionCode", $this->requestLocator["LocationSearchCriteria"]["SearchOption"])) {
                    $optiontype->appendChild($this->addOptionsToNode($this->requestLocator["LocationSearchCriteria"]["SearchOption"]["OptionCode"], $xml->createElement("OptionCode"), $xml));
                }

                //Relation
                if (array_key_exists("Relation", $this->requestLocator["LocationSearchCriteria"]["SearchOption"])) {



                    $relation = $SearchOption->appendChild($xml->createElement("Relation"));
                    //code
                    if (array_key_exists("Code", $this->requestLocator["LocationSearchCriteria"]["SearchOption"]["Relation"])) {
                        $relatiom->appendChild($xml->createElement("Code", $this->requestLocator["LocationSearchCriteria"]["SearchOption"]["Relation"]["code"]));
                    }
                }
//                    $SearchOption->appendChild($this->addOptionsToNode($this->requestLocator["LocationSearchCriteria"]["SearchOption"]["OptionType"], $xml->createElement("OptionType"), $xml));
            }//end of searchoption
            //MaximumListSize
            if (array_key_exists("MaximumListSize", $this->requestLocator["LocationSearchCriteria"])) {
                $LocationSearchCriteria->appendChild($xml->createElement("MaximumListSize"), $this->requestLocator["LocationSearchCriteria"]["MaximumListSize"]);
            }

            //SearchRadius
            if (array_key_exists("SearchRadius", $this->requestLocator["LocationSearchCriteria"])) {
                $LocationSearchCriteria->appendChild($xml->createElement("SearchRadius"), $this->requestLocator["LocationSearchCriteria"]["SearchRadius"]);
            }
            //ServiceSearch
            if (array_key_exists("ServiceSearch", $this->requestLocator["LocationSearchCriteria"])) {
                $ServiceSearch = $LocationSearchCriteria->appendChild($xml->createElement("ServiceSearch"), $this->requestLocator["LocationSearchCriteria"]["ServiceSearch"]);
                //Time
                if (array_key_exists("Time", $this->requestLocator["LocationSearchCriteria"]["ServiceSearch"])) {
                    $LocationSearchCriteria->appendChild($xml->createElement("Time"), $this->requestLocator["LocationSearchCriteria"]["MaximumListSize"]);
                }
                //ServiceCode
                //ServiceOptionCode
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
    private function formatResponse(SimpleXMLElement $response) {
        return $this->convertXmlObject($response);
    }

    /**
     * @return RequestInterface
     */
    public function getRequest() {
        if (null === $this->request) {
            $this->request = new Request;
        }
        return $this->request;
    }

    /**
     * @param RequestInterface $request
     * @return $this
     */
    public function setRequest(RequestInterface $request) {
        $this->request = $request;
        return $this;
    }

    /**
     * @return ResponseInterface
     */
    public function getResponse() {
        return $this->response;
    }

    /**
     * @param ResponseInterface $response
     * @return $this
     */
    public function setResponse(ResponseInterface $response) {
        $this->response = $response;
        return $this;
    }

    /**
     * Add Options To Node
     *
     * @param array $options
     * @param SimpleXMLElement $node
     * @param DOMDocument $xml
     */
    private function addOptionsToNode($options, $node, $xml) {
        foreach ($options as $key => $value) {
            $node->appendChild($xml->createElement($key, $value));
        }
        return $node;
    }

}
