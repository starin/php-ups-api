<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

include("../../tests/bootstrap.php");

use Ups;
$accessKey="Add Access Key Here";
$userId="Add UserName Here";
$password="Add Password Here";

testFreightPickup($accessKey, $userId, $password);
//testFreightCancelPickup($accessKey, $userId, $password);
function testFreightPickup($accessKey, $userId, $password)
{
    echo 'PickupFreight:<br/>';
    $pickupFreight = new Ups\PickupFreight($accessKey, $userId, $password, t);

    try
    {
        $res = $pickupFreight->pickupRequest("ProcessFreightPickup", processFreightPickup());
        var_dump(json_encode($res));
        echo '<br/><br/><br/>';

    } catch (Exception $ex) {
        echo "ex.......<br/>";
        var_dump(json_encode($ex));
        echo '<br/><br/><br/>';
    }
}


function testFreightCancelPickup($accessKey, $userId, $password)
{
    echo 'FreightCancelPickup:<br/>';
    $pickupFreight = new Ups\PickupFreight($accessKey, $userId, $password, true);

    try
    {
        $res = $pickupFreight->pickupRequest("ProcessFreightCancelPickup", processFreightCancelPickup());
        var_dump(json_encode($res));
        echo '<br/><br/><br/>';

    } catch (Exception $ex) {
        echo "ex.......<br/>";
        var_dump(json_encode($ex));
        echo '<br/><br/><br/>';
    }
}
function processFreightPickup()
{
    //create soap request    
    $requestoption['RequestOption'] = '1';
    $request['Request'] = $requestoption;

    $request['DestinationCountryCode'] = 'US';
    
    //Requester
    $requester['Name'] = 'ABC Associates';
    $requester['AttentionName'] = 'Mr. ABC';
    $requester['EMailAddress'] = 'wbb6tdf@ups.com';
    $phone1['Number'] = '123456789';
    $phone1['Extension'] = '345';
    $requester['Phone'] = $phone1;
    $requester['ThirdPartyIndicator'] = '1';
    $request['Requester'] = $requester;

    //ShipFrom
    $shipfrom['Name'] = 'ABC Associates';
    $shipfrom['AttentionName'] = 'Mr. ABC';
    $addressline1 = array('AddressLine1');
    $address1['AddressLine'] = $addressline1;
    $address1['City'] = 'Roswell';
    $address1['StateProvinceCode'] = 'GA';
    $address1['CountryCode'] = 'US';
    $address1['PostalCode'] = '30076';
    $shipfrom['Address'] = $address1;
    $phone2['Number'] = '123456789';
    $phone2['Extension'] = '345';
    $shipfrom['Phone'] = $phone2;
    $request['ShipFrom'] = $shipfrom;

    //ShipTo
    $shipto['AttentionName'] = '';
    $address2['AddressLine'] = '';
    $address2['City'] = '';
    $address2['StateProvinceCode'] = '';
    $address2['CountryCode'] = '';
    $shipto['Address'] = $address2;
    $request['ShipTo'] = $shipto;

    //ShipmentDetail
    $packagingtype['Code'] = 'BAR';
    $packagingtype['Description'] = 'BARREL';
    $shipmentdetail['PackagingType'] = $packagingtype;
    $shipmentdetail['NumberOfPieces'] = '5';
    $shipmentdetail['DescriptionOfCommodity'] = 'fdqwd';
    $unit['Code'] = 'LBS';
    $unit['Description'] = 'Pounds';
    $weight['UnitOfMeasurement'] = $unit;
    $weight['Value'] = '250.78';
    $shipmentdetail['Weight'] = $weight;
    $request['ShipmentDetail'] = $shipmentdetail;

    $request['PickupDate'] = '20141223';
    $request['EarliestTimeReady'] =  '0800';
    $request['LatestTimeReady'] = '1800';

    echo "Request.......<br/>";
    var_dump(json_encode($request));
    echo '<br/><br/><br/>';

    return $request;
}
function processFreightCancelPickup()
{
    //create soap request    
    $requestoption['RequestOption'] = '1';
    $request['Request'] = $requestoption;
    $request['PickupRequestConfirmationNumber'] = '123';

    echo "Request.......<br/>";
    var_dump(json_encode($request));
    echo '<br/><br/><br/>';

    return $request;
}