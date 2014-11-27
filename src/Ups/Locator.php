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
        return $request;
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



//                    $optiontype = $SearchOption->appendChild($xml->createElement("OptionType"));
                    //code
//                    if (array_key_exists("Code", $this->requestLocator["LocationSearchCriteria"]["SearchOption"]["OptionType"])) {
                    $SearchOption->appendChild($this->addOptionsToNode($this->requestLocator["LocationSearchCriteria"]["SearchOption"]["OptionType"], $xml->createElement("OptionType"), $xml));    
//                    $SearchOption->appendChild($xml->createElement("OptionType", $this->requestLocator["LocationSearchCriteria"]["SearchOption"]["OptionType"]));
//                    }
                }
                //CodeType
                if (array_key_exists("SelectedOptionCode", $this->requestLocator["LocationSearchCriteria"]["SearchOption"])) {
                    
                    foreach ($this->requestLocator["LocationSearchCriteria"]["SearchOption"]["SelectedOptionCode"] as $key => $optionCode) {
                     $SearchOption->appendChild($this->addOptionsToNode("$optionCode", $xml->createElement("$key"), $xml));   
                    }
//                    $SearchOption->appendChild($this->addOptionsToNode($this->requestLocator["LocationSearchCriteria"]["SearchOption"]["OptionCode"], $xml->createElement("OptionCode"), $xml));
                }

                //Relation
                if (array_key_exists("Relation", $this->requestLocator["LocationSearchCriteria"]["SearchOption"])) {



                    $relation = $SearchOption->appendChild($xml->createElement("Relation"));
                    //code
                    if (array_key_exists("Code", $this->requestLocator["LocationSearchCriteria"]["SearchOption"]["Relation"])) {
                        $relatiom->appendChild($xml->createElement("Code", $this->requestLocator["LocationSearchCriteria"]["SearchOption"]["Relation"]["code"]));
                    }
                }
            }
                
                
                
                
                
            
//                    $SearchOption->appendChild($this->addOptionsToNode($this->requestLocator["LocationSearchCriteria"]["SearchOption"]["OptionType"], $xml->createElement("OptionType"), $xml));
                //MaximumListSize
                if (array_key_exists("MaximumListSize", $this->requestLocator["LocationSearchCriteria"])) {
                    $LocationSearchCriteria->appendChild($xml->createElement("MaximumListSize", $this->requestLocator["LocationSearchCriteria"]["MaximumListSize"]));
                }


                //SearchRadius
                if (array_key_exists("SearchRadius", $this->requestLocator["LocationSearchCriteria"])) {
                    $LocationSearchCriteria->appendChild($xml->createElement("SearchRadius", $this->requestLocator["LocationSearchCriteria"]["SearchRadius"]));
                }

                //ServiceSearch
                if (array_key_exists("ServiceSearch", $this->requestLocator["LocationSearchCriteria"])) {
                    $ServiceSearch = $LocationSearchCriteria->appendChild($xml->createElement("ServiceSearch"));
                    //Time
                    if (array_key_exists("Time", $this->requestLocator["LocationSearchCriteria"]["ServiceSearch"])) {
                        $ServiceSearch->appendChild($xml->createElement("Time", $this->requestLocator["LocationSearchCriteria"]["ServiceSearch"]["Time"]));
                    }
                    //ServiceCode
                    if (array_key_exists("ServiceCode", $this->requestLocator["LocationSearchCriteria"]["ServiceSearch"])) {


                        $ServiceSearch->appendChild($this->addOptionsToNode($this->requestLocator["LocationSearchCriteria"]["ServiceSearch"]["ServiceCode"], $xml->createElement("ServiceCode"), $xml));
                    }
                    //ServiceOptionCode
                    if (array_key_exists("ServiceOptionCode", $this->requestLocator["LocationSearchCriteria"]["ServiceSearch"])) {


                        $ServiceSearch->appendChild($this->addOptionsToNode($this->requestLocator["LocationSearchCriteria"]["ServiceSearch"]["ServiceOptionCode"], $xml->createElement("ServiceOptionCode"), $xml));
                    }
                }


                //WillCallSearch>SLIC PostcodePrimaryLow CountryCode

                if (array_key_exists("WillCallSearch", $this->requestLocator["LocationSearchCriteria"])) {


                    $LocationSearchCriteria->appendChild($this->addOptionsToNode($this->requestLocator["LocationSearchCriteria"]["WillCallSearch"], $xml->createElement("WillCallSearch"), $xml));
                }
                /*                 * FreightWillCallSearch
                  |
                  -------------------------------------------------------------------------------------------------------
                  |                               |                  |                         |                   |
                  FreightWillCallRequestType       FacilityAddress    OriginOrDestination    FormatPostalCode    DayOfWeekCode
                 *                           |
                 *                       all string    
                 * 
                 */
                if (array_key_exists("FreightWillCallSearch", $this->requestLocator["LocationSearchCriteria"])) {
                    $FreightWillCallSearch = $LocationSearchCriteria->appendChild($xml->createElement("FreightWillCallSearch"));
                    $FreightWillCallSearch->appendChild($xml->createElement("FreightWillCallRequestType",$this->requestLocator["LocationSearchCriteria"]["FreightWillCallSearch"]["FreightWillCallRequestType"]));
                    if (array_key_exists("FacilityAddress", $this->requestLocator["LocationSearchCriteria"]["FreightWillCallSearch"])) {
                        $FreightWillCallSearch->appendChild($this->addOptionsToNode($this->requestLocator["LocationSearchCriteria"]["WillCallSearch"]["FacilityAddress"], $xml->createElement("FacilityAddress"), $xml));
                    }
                    if (array_key_exists("OriginOrDestination", $this->requestLocator["LocationSearchCriteria"]["FreightWillCallSearch"])) {
                        $FreightWillCallSearch->appendChild($xml->createElement("OriginOrDestination",$this->requestLocator["LocationSearchCriteria"]["FreightWillCallSearch"]["OriginOrDestination"]));
                    }
                    if (array_key_exists("FormatPostalCode", $this->requestLocator["LocationSearchCriteria"]["FreightWillCallSearch"])) {
                        $FreightWillCallSearch->appendChild($xml->createElement("DayOfWeekCode",$this->requestLocator["LocationSearchCriteria"]["FreightWillCallSearch"]["DayOfWeekCode"]));
                    }
                    if (array_key_exists("FormatPostalCode", $this->requestLocator["LocationSearchCriteria"]["FreightWillCallSearch"])) {
                        $FreightWillCallSearch->appendChild($xml->createElement("DayOfWeekCode",$this->requestLocator["LocationSearchCriteria"]["FreightWillCallSearch"]["FormatPostalCode"]));
                    }
                }
                /**                                     AccessPointSearch
                  |
                  ---------------------------------------------------------------------------------------------------------------------
                 *         |                     |                    |              |                                              |
                 * PublicAccessPointID    AccessPointStatus    AccountNumber   IncludeCriteria                             ExcludeFromResult
                 *                                                                   |                                             |
                 *                                                 -----------------------------------                        ---------------------------------------------------------------------------------------
                 *                                                 |                |                 |                       |                                  |             |                   |                 
                 *                                                 |                |                 |                     BusinessClassificationCode     BusinessName      Radius     PostalCodeList            
                 *                                    MerchantAccountNumberList  SearchFilter   ServiceOfferingList                                                                            |
                 *                                               |                    |                 |                                                                                   PostalCode
                 *                                       string                      string          ServiceOffering                                                                            |
                 *                                                                                         |                                                                                string
                 *                                                                                      string              
                 *                                                                                       
                 *                                     
                 */
                if (array_key_exists("AccessPointSearch", $this->requestLocator["LocationSearchCriteria"])) {
                    $AccessPointSearch = $LocationSearchCriteria->appendChild($xml->createElement("AccessPointSearch"));
                    //PublicAccessPointID
                    if (array_key_exists("PublicAccessPointID", $this->requestLocator["LocationSearchCriteria"]["AccessPointSearch"])) {
                        $AccessPointSearch->appendChild($xml->createElement("PublicAccessPointID", $this->requestLocator["LocationSearchCriteria"]["AccessPointSearch"]["PublicAccessPointID"]));
                    }
                    //AccessPointStatus
                    if (array_key_exists("AccessPointStatus", $this->requestLocator["LocationSearchCriteria"]["AccessPointSearch"])) {
                        $AccessPointSearch->appendChild($xml->createElement("AccessPointStatus", $this->requestLocator["LocationSearchCriteria"]["AccessPointSearch"]["AccessPointStatus"]));
                    }
                    //AccountNumber
                    if (array_key_exists("AccountNumber", $this->requestLocator["LocationSearchCriteria"]["AccessPointSearch"])) {
                        $AccessPointSearch->appendChild($xml->createElement("AccountNumber", $this->requestLocator["LocationSearchCriteria"]["AccessPointSearch"]["AccountNumber"]));
                    }
                    //IncludeCriteria
                    if (array_key_exists("IncludeCriteria", $this->requestLocator["LocationSearchCriteria"]["AccessPointSearch"])) {
                        $IncludeCriteria = $AccessPointSearch->appendChild($xml->createElement("IncludeCriteria"));
                        //MerchantAccountNumberList
                        if (array_key_exists("MerchantAccountNumberList", $this->requestLocator["LocationSearchCriteria"]["AccessPointSearch"]["IncludeCriteria"])) {
                            $IncludeCriteria->appendChild($this->addOptionsToNode($this->requestLocator["LocationSearchCriteria"]["AccessPointSearch"]["IncludeCriteria"]["MerchantAccountNumberList"], $xml->createElement("MerchantAccountNumberList"), $xml));
                        }
                        //SearchFilter
                        if (array_key_exists("SearchFilter", $this->requestLocator["LocationSearchCriteria"]["AccessPointSearch"]["IncludeCriteria"])) {
                            $IncludeCriteria->appendChild($this->addOptionsToNode($this->requestLocator["LocationSearchCriteria"]["AccessPointSearch"]["IncludeCriteria"]["SearchFilter"], $xml->createElement("SearchFilter"), $xml));
                        }
                        //ServiceOfferingList
                        if (array_key_exists("ServiceOfferingList", $this->requestLocator["LocationSearchCriteria"]["AccessPointSearch"]["IncludeCriteria"])) {
                            $ServiceOfferingList = $IncludeCriteria->appendChild($xml->createElement("ServiceOfferingList"));
                            //ServiceOffering
                            if (array_key_exists("ServiceOffering", $$this->requestLocator["LocationSearchCriteria"]["AccessPointSearch"]["IncludeCriteria"]["ServiceOfferingList"])) {
                                $ServiceOfferingList->appendChild($this->addOptionsToNode($this->requestLocator["LocationSearchCriteria"]["AccessPointSearch"]["IncludeCriteria"]["ServiceOfferingList"]["ServiceOffering"], $xml->createElement("ServiceOffering"), $xml));
                            }
                        }
                    }
                    //ExcludeFromResult
                    if (array_key_exists("ExcludeFromResult", $this->requestLocator["LocationSearchCriteria"]["AccessPointSearch"])) {
                        $ExcludeFromResult = $AccessPointSearch->appendChild($xml->createElement("ExcludeFromResult"));
                        //BusinessClassificationCode
                        if (array_key_exists("BusinessClassificationCode", $this->requestLocator["LocationSearchCriteria"]["AccessPointSearch"]["ExcludeFromResult"])) {
                            $ExcludeFromResult->appendChild($xml->createElement("BusinessClassificationCode", $this->requestLocator["LocationSearchCriteria"]["AccessPointSearch"]["ExcludeFromResult"]["BusinessClassificationCode"]));
                        }
                        //BusinessName
                        if (array_key_exists("BusinessName", $this->requestLocator["LocationSearchCriteria"]["AccessPointSearch"]["ExcludeFromResult"])) {
                            $ExcludeFromResult->appendChild($xml->createElement("BusinessName", $this->requestLocator["LocationSearchCriteria"]["AccessPointSearch"]["ExcludeFromResult"]["BusinessName"]));
                        }
                        //Radius
                        if (array_key_exists("Radius", $this->requestLocator["LocationSearchCriteria"]["AccessPointSearch"]["ExcludeFromResult"])) {
                            $ExcludeFromResult->appendChild($xml->createElement("Radius", $this->requestLocator["LocationSearchCriteria"]["AccessPointSearch"]["ExcludeFromResult"]["Radius"]));
                        }
                        //PostalCodeList
                        if (array_key_exists("PostalCodeList", $this->requestLocator["LocationSearchCriteria"]["AccessPointSearch"]["ExcludeFromResult"])) {
                            $PostalCodeList = $ExcludeFromResult->appendChild($xml->createElement("PostalCodeList"));
                            //PostalCode
                            if (array_key_exists("PostalCode", $$this->requestLocator["LocationSearchCriteria"]["AccessPointSearch"]["ExcludeFromResult"]["PostalCodeList"])) {
                                $PostalCodeList->appendChild($this->addOptionsToNode($this->requestLocator["LocationSearchCriteria"]["AccessPointSearch"]["ExcludeFromResult"]["PostalCodeList"]["PostalCode"], $xml->createElement("PostalCode"), $xml));
                            }
                        }
                    }
                }
            }//end of locationsearch criteria
            /*             * *
             * 
             * MapNavigation
             * **************
             * @MapDimensions>string
             * Number
             * ZoomFactor
             * PanX
             * PanY
             * MapID
             * MapURL
             * @ ImageMap@string
             */
            if (array_key_exists("MapNavigation", $this->requestLocator)) {
                $MapNavigation = $loc_request->appendChild($xml->createElement("MapNavigation"));
                //MapDimensions
                if (array_key_exists("MapDimensions", $$this->requestLocator["MapNavigation"])) {
                    $MapNavigation->appendChild($this->addOptionsToNode($this->requestLocator["MapNavigation"]["MapDimensions"], $xml->createElement("MapDimensions"), $xml));
                }
                //Number
                if (array_key_exists("Number", $this->requestLocator["MapNavigation"])) {
                    $MapNavigation->appendChild($xml->createElement("Number", $this->requestLocator["MapNavigation"]["Number"]));
                }
                //ZoomFactor
                if (array_key_exists("ZoomFactor", $this->requestLocator["MapNavigation"])) {
                    $MapNavigation->appendChild($xml->createElement("ZoomFactor", $this->requestLocator["MapNavigation"]["ZoomFactor"]));
                }
                //PanX
                if (array_key_exists("PanX", $this->requestLocator["MapNavigation"])) {
                    $MapNavigation->appendChild($xml->createElement("PanX", $this->requestLocator["MapNavigation"]["PanX"]));
                }
                //PanY
                if (array_key_exists("PanY", $this->requestLocator["MapNavigation"])) {
                    $MapNavigation->appendChild($xml->createElement("PanY", $this->requestLocator["MapNavigation"]["PanY"]));
                }
                //MapID
                if (array_key_exists("MapID", $this->requestLocator["MapNavigation"])) {
                    $MapNavigation->appendChild($xml->createElement("MapID", $this->requestLocator["MapNavigation"]["MapID"]));
                }
                //MapID
                if (array_key_exists("MapURL", $this->requestLocator["MapNavigation"])) {
                    $MapNavigation->appendChild($xml->createElement("MapURL", $this->requestLocator["MapNavigation"]["MapURL"]));
                }
                //MapDimensions
                if (array_key_exists("ImageMap", $$this->requestLocator["MapNavigation"])) {
                    $MapNavigation->appendChild($this->addOptionsToNode($this->requestLocator["MapNavigation"]["ImageMap"], $xml->createElement("ImageMap"), $xml));
                }
            }
            /*
             * AllowAllConfidenceLevels
             * @string
             */
            if (array_key_exists("AllowAllConfidenceLevels", $this->requestLocator)) {
                $loc_request->appendChild($xml->createElement("AllowAllConfidenceLevels"));
            }
            /**
             * AllowAllConfidenceLevels
             * @string
             */
            if (array_key_exists("AllowAllConfidenceLevels", $this->requestLocator)) {
                $loc_request->appendChild($xml->createElement("AllowAllConfidenceLevels"));
            }
            /*             * *
             * ServiceGeoUnit
             * @type other
             */
            if (array_key_exists("ServiceGeoUnit", $this->requestLocator)) {
                $loc_request->appendChild($this->addOptionsToNode($this->requestLocator["ServiceGeoUnit"], $xml->createElement("ServiceGeoUnit"), $xml));
            }
            /**
             * FreightIndicator
             */
            if (array_key_exists("ServiceGeoUnit", $this->requestLocator)) {
                $loc_request->appendChild($xml->createElement("ServiceGeoUnit"));
            }
        
        
        return $xml->saveXML();
    }

    public function addElementsTreeNode($xmlArray, $node, $xml) {
        $rootNode = $node;

        $i = 0;
        if (is_array($xmlArray)) {
            foreach ($xmlArray as $key => $sub_array) {

                if (is_array($sub_array)) {

                    $rootNode = $this->createChildNode($rootNode, $xml, $key);
                } else {
                    $this->createChildNode($rootNode, $xml, $key, $sub_array);
                }
                if (array_key_exists($key, $xmlArray)) {
                    $rootNode = $node;
                }
            }
        }
        return $node;
    }

    private function createChildNode($rootNode, $xml, $key, $value = NULL) {
        if (empty($value)) {
            return $rootNode->appendChild($xml->createElement("$key"));
        } else {
            $rootNode->appendChild($xml->createElement("$key", "$value"));
        }
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
