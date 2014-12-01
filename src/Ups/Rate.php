<?php

namespace Ups;

use DOMDocument;
use DOMElement;
use SimpleXMLElement;
use Exception;
use stdClass;
use Ups\Entity\RateRequest;
use Ups\Entity\RateResponse;
use Ups\Entity\Shipment;

/**
 * Rate API Wrapper
 *
 * @package ups
 * @author Michael Williams <michael.williams@limelyte.com>
 */
class Rate extends Ups {

    const ENDPOINT = '/Rate';

    /**
     * @var RequestInterface
     */
    private $request;

    /**
     * @var ResponseInterface
     * todo: make private
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
    public function __construct($accessKey = null, $userId = null, $password = null, $useIntegration = FALSE) {
//        $this->requestOption = array();
        parent::__construct($accessKey, $userId, $password, $useIntegration);
    }

    /**
     * @param $rateRequest
     * @return RateRequest
     * @throws Exception
     */
    public function shopRates($rateRequest) {
        $this->requestOption = "Shop";

        return $this->sendRequest($rateRequest);
    }

    /**
     * @param $rateRequest
     * @return RateRequest
     * @throws Exception
     */
    public function getRate($rateRequest) {
        if ($rateRequest instanceof Shipment) {
            $shipment = $rateRequest;
            $rateRequest = new RateRequest();
            $rateRequest->setShipment($shipment);
        }

        $this->requestOption = "Rate";

        return $this->sendRequest($rateRequest);
    }

    /**
     * Creates and sends a request for the given shipment. This handles checking for
     * errors in the response back from UPS
     *
     * @param RateRequest $rateRequest
     * @return RateRequest
     * @throws Exception
     */
    private function sendRequest(RateRequest $rateRequest) {
//    public function sendRequest()
        $request = $this->createRequest($rateRequest);



        $this->response = $this->getRequest()->request($this->createAccess(), $request, $this->compileEndpointUrl(self::ENDPOINT));
        $response = $this->response->getResponse();
        if (null === $response) {
            throw new Exception("Failure (0): Unknown error", 0);
        }

        if ($response->Response->ResponseStatusCode == 0) {
            throw new Exception(
            "Failure ({$response->Response->Error->ErrorSeverity}): {$response->Response->Error->ErrorDescription}", (int) $response->Response->Error->ErrorCode
            );
        } else {
            return $this->formatResponse($response);
        }
    }

    /**
     * Create the Rate request
     *
     * @param RateRequest $rateRequest The request details. Refer to the UPS documentation for available structure
     * @return string
     */
    private function createRequest(RateRequest $rateRequest) {
        $shipment = $rateRequest->getShipment();

        $document = $xml = new DOMDocument();
        $xml->formatOutput = true;

        /** @var DOMElement $trackRequest */
        $trackRequest = $xml->appendChild($xml->createElement("RatingServiceSelectionRequest"));
        $trackRequest->setAttribute('xml:lang', 'en-US');

        $request = $trackRequest->appendChild($xml->createElement("Request"));

        $node = $xml->importNode($this->createTransactionNode(), true);
//        var_dump(json_en$node));
//        $request->appendChild($node);

        $request->appendChild($xml->createElement("RequestAction", "Rate"));
        $request->appendChild($xml->createElement("RequestOption", $this->requestOption));

        $trackRequest->appendChild($rateRequest->getPickupType()->toNode($document));

        $customerClassification = $rateRequest->getCustomerClassification();
        if (isset($customerClassification)) {
//            $trackRequest->appendChild($customerClassification->toNode($document));
        }

        $shipmentNode = $trackRequest->appendChild($xml->createElement('Shipment'));
        $shipmentNode->appendChild($xml->createElement("Description", $shipment->getDescription()));


        $shipper = $shipment->getShipper();
        if (isset($shipper)) {
            $shipmentNode->appendChild($shipper->toNode($document));
        }
        $shipTo = $shipment->getShipTo();
        if (isset($shipTo)) {
            $shipmentNode->appendChild($shipTo->toNode($document));
        }
        $shipFrom = $shipment->getShipFrom();
        if (isset($shipFrom)) {
            $shipmentNode->appendChild($shipFrom->toNode($document));
        }

        // Support specifying an individual service
        $service = $shipment->getService();
        if (isset($service)) {

            $shipmentNode->appendChild($service->toNode($document));
        }

        foreach ($shipment->getPackages() as $package) {
            $shipmentNode->appendChild($package->toNode($document));
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
        // We don't need to return data regarding the response to the user
        unset($response->Response);

        $result = $this->convertXmlObject($response);

        return new RateResponse($result);
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

}
