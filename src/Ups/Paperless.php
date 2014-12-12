<?php


namespace Ups;

//use DOMDocument;
//use SimpleXMLElement;
use Exception;
use stdClass;
use SoapClient;
class Paperless extends Ups {
    //put your code here
     const ENDPOINT = '/PaperlessDocumentAPI';
    const WSDL_EXT = "/Paperless/PaperlessDocumentAPI.wsdl";
    
    public $CustomerContext;
    public $TransactionIdentifier;
    public $ShipperNumber;
    public $FileName;
    public $FileText;
    public $FileFormat;
    public $FileDocumentType;
    public $DocumentID;
    public $FormsHistoryDocumentID;
    public $GroupID;
    public $ShipmentIdentifier;
    public $ShipmentType;
    public $ShipmentDateAndTime;
    public $TrackingNumber;
    public $PRQConfirmationNumber;
    


    /**
     * @var string
     */
//    private $productionBaseWebUrl = 'https://filexfer.ups.com/webservices';

    /**
     * @var string
     */
//    private $integrationBaseWebUrl = 'https://wwwcie.ups.com/webservices';
    
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
//   private $
private $customerContext;
private $transactionIdentifier;
private $shipperNumber;
private $fileName;
private $fileText;
private $fileFormat;
private $fileDocumentType;
private $documentID;
private $formsHistoryDocumentID;
private $groupID;
private $shipmentIdentifier;
private $shipmentType;
private $shipmentDateAndTime;
private $trackingNumber;
private $pRQConfirmationNumber;
    /**
     * @param string|null $accessKey UPS License Access Key
     * @param string|null $userId UPS User ID
     * @param string|null $password UPS User Password
     * @param bool $useIntegration Determine if we should use production or CIE URLs.
     */    
    public function __construct($accessKey = null, $userId = null, $password = null, $useIntegration = false)
    {
        $this->productionBaseWebUrl = 'https://filexfer.ups.com/webservices';
        $this->integrationBaseWebUrl = 'https://wwwcie.ups.com/webservices';
        parent::__construct($accessKey, $userId, $password, $useIntegration);
        
        $this->initSoapClient();
    }
    
    /**
     * Pickup Freight Request
     *
     * @param string $operation The operation/function name 
     * @param object $requestOptions
     * @return stdClass
     * @throws Exception
     */
    public function PaperLessRequest($operation)
    {
        $this->operation = $operation;
        
        $this->requestOptions =$this->$operation();
       

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
     * Set Soap Client
     */
    private function initSoapClient()
    {
        // initialize soap client
        $this->soapClient = $this->getSoapClient(self::WSDL_EXT);
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
      function processUploading()
  {
      //create soap request
    $request['Request'] = array
    (
       'TransactionReference' => array
       (
            'CustomerContext' => $this->getCustomerContext(),
            'TransactionIdentifier' => $this->getTransactionIdentifier(),
       )
    );
    $request['ShipperNumber'] = $this->getShipperNumber();
    $request['UserCreatedForm'] = array
    (
        'UserCreatedFormFileName' => $this->getFileName(),
		'UserCreatedFormFile' =>  $this->getFileText(),
		'UserCreatedFormFileFormat' => $this->getFileFormat(),
		'UserCreatedFormDocumentType' => $this->getFileDocumentType(),
    );

    return $request;

  }

  function processDeleting()
  {
      //create soap request
    $request['Request'] = array
    (
       'TransactionReference' => array
       (
            'CustomerContext' => $this->getCustomerContext(),
            'TransactionIdentifier' => $this->getTransactionIdentifier()
       )
    );
    $request['ShipperNumber'] = $this->getShipperNumber();
	$request['DocumentID'] = $this->getDocumentID();
    return $request;
  }

  function processPushToImageRepository()
  {
    //create soap request
    $request['Request'] = array
    (
       'TransactionReference' => array
       (
            'CustomerContext' => $this->getCustomerContext(),
            'TransactionIdentifier' => $this->getTransactionIdentifier(),
       )
    );
    
    $request['ShipperNumber'] = $this->getShipperNumber();
	$request['FormsHistoryDocumentID'] = array
	(
		 'DocumentID' => $this->getFormsHistoryDocumentID(),
	);
	$request['FormsGroupID'] = $this->getGroupID();  // Set the value to Forms Group ID if one needs to update existing Forms Group ID with new Document ID and push it to Image Repository.
	$request['ShipmentIdentifier'] = $this->getShipmentIdentifier();
	$request['ShipmentType'] = $this->getShipmentType(); // For small package shipment, set this field to value "1". For freight shipment, set this field to value "2".

	if(strcmp($request['ShipmentType'] , '1') == 0 )
	{
	    $request['ShipmentDateAndTime'] = $this->getShipmentDateAndTime();
	 	$request['TrackingNumber'] = $this->getTrackingNumber();
	}
	else
	{
	    $request['PRQConfirmationNumber'] = $this->getPRQConfirmationNumber();
	}
    return $request;
  }
  
      /**
     * @return string
     */
    public function getCustomerContext()
    {
        return $this->customerContext;
    }

    /**
     * @param string $customerContext
     * @return $this
     */
    public function setCustomerContext($customerContext)
    {
        $this->CustomerContext = $customerContext;
        $this->customerContext = $customerContext;
        return $this;
    }
    
    //TransactionIdentifier
       /**
     * @return string
     */
    public function getTransactionIdentifier()
    {
        return $this->transactionIdentifier;
    }

    /**
     * @param string $transactionIdentifier
     * @return $this
     */
    public function settransactionIdentifier($transactionIdentifier)
    {
        $this->TransactionIdentifier = $transactionIdentifier;
        $this->transactionIdentifier = $transactionIdentifier;
        return $this;
    }
    //ShipperNumber
        /**
     * @return string
     */
    public function getShipperNumber()
    {
        return $this->shipperNumber;
    }

    /**
     * @param string $shipperNumber
     * @return $this
     */
    public function setShipperNumber($shipperNumber)
    {
        $this->ShipperNumber = $shipperNumber;
        $this->shipperNumber = $shipperNumber;
        return $this;
    }
    //FileName
          /**
     * @return string
     */
    public function getFileName()
    {
        return $this->fileName;
    }

    /**
     * @param string $fileName
     * @return $this
     */
    public function setFileName($fileName)
    {
        $this->FileName = $fileName;
        $this->fileName = $fileName;
        return $this;
    }
    
       //FileText
      /**
     * @return string
     */
    public function getFileText()
    {
        return $this->fileText;
    }

    /**
     * @param string $fileText
     * @return $this
     */
    public function setFileText($fileText)
    {
        $this->FileText = $fileText;
        $this->fileText = $fileText;
        return $this;
    }
    //FileFormat
    /**
     * @return string
     */
    public function getFileFormat()
    {
        return $this->fileFormat;
    }

    /**
     * @param string $fileFormat
     * @return $this
     */
    public function setFileFormat($fileFormat)
    {
        $this->FileFormat = $fileFormat;
        $this->fileFormat = $fileFormat;
        return $this;
    }
    //DocumentType
     /**
     * @return string
     */
    public function getFileDocumentType()
    {
        return $this->fileDocumentType;
    }

    /**
     * @param string $documentType
     * @return $this
     */
    public function setFileDocumentType($fileDocumentType)
    {
        $this->FileDocumentType = $fileDocumentType;
        $this->fileDocumentType = $fileDocumentType;
        return $this;
    }
    //DocumentID
        /**
     * @return string
     */
    public function getDocumentID()
    {
        return $this->documentID;
    }

    /**
     * @param string $documentID
     * @return $this
     */
    public function setDocumentID($documentID)
    {
        $this->DocumentID = $documentID;
        $this->documentID = $documentID;
        return $this;
    }
    //FormsHistoryDocumentID
         /**
     * @return string
     */
    public function getFormsHistoryDocumentID()
    {
        return $this->formsHistoryDocumentID;
    }

    /**
     * @param string $formsHistoryDocumentID
     * @return $this
     */
    public function setFormsHistoryDocumentID($formsHistoryDocumentID)
    {
        $this->FormsHistoryDocumentID = $formsHistoryDocumentID;
        $this->formsHistoryDocumentID = $formsHistoryDocumentID;
        return $this;
    }
//    GroupID
          /**
     * @return string
     */
    public function getGroupID()
    {
        return $this->groupID;
    }

    /**
     * @param string $groupID
     * @return $this
     */
    public function setGroupID($groupID)
    {
        $this->GroupID = $groupID;
        $this->groupID = $groupID;
        return $this;
    }
    //ShipmentIdentifier
             /**
     * @return string
     */
    public function getShipmentIdentifier()
    {
        return $this->shipmentIdentifier;
    }

    /**
     * @param string $shipmentIdentifier
     * @return $this
     */
    public function setShipmentIdentifier($shipmentIdentifier)
    {
        $this->ShipmentType = $shipmentIdentifier;
        $this->shipmentIdentifier = $shipmentIdentifier;
        return $this;
    }
    //ShipmentType
                 /**
     * @return string
     */
    public function getShipmentType()
    {
        return $this->shipmentType;
    }

    /**
     * @param string $shipmentType
     * @return $this
     */
    public function setShipmentType($shipmentType)
    {
        $this->ShipmentType = $shipmentType;
        $this->shipmentType = $shipmentType;
        return $this;
    }
    //ShipmentDateAndTime
                  /**
     * @return string
     */
    public function getShipmentDateAndTime()
    {
        return $this->shipmentDateAndTime;
    }

    /**
     * @param string $shipmentDateAndTime
     * @return $this
     */
    public function setShipmentDateAndTime($shipmentDateAndTime)
    {
        $this->ShipmentDateAndTime = $shipmentDateAndTime;
        $this->shipmentDateAndTime = $shipmentDateAndTime;
        return $this;
    }
    //TrackingNumber
    /**
     * @return string
     */
    public function getTrackingNumber()
    {
        return $this->trackingNumber;
    }

    /**
     * @param string $trackingNumber
     * @return $this
     */
    public function setTrackingNumber($trackingNumber)
    {
        $this->TrackingNumber = $trackingNumber;
        $this->trackingNumber = $trackingNumber;
        return $this;
    }
    //PRQConfirmationNumber
     /**
     * @return string
     */
    public function getPRQConfirmationNumber()
    {
        return $this->pRQConfirmationNumber;
    }

    /**
     * @param string $pRQConfirmationNumber
     * @return $this
     */
    public function setPRQConfirmationNumber($pRQConfirmationNumber)
    {
        $this->PRQConfirmationNumber = $pRQConfirmationNumber;
        $this->pRQConfirmationNumber = $pRQConfirmationNumber;
        return $this;
    }
}
