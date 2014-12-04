<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);

include("../../tests/bootstrap.php");

use Ups;
$accessKey="Add Access Key Here";
$userId="Add UserName Here";
$password="Add Password Here";

    echo 'testPickup:<br/>';
    $pickupFreight = new Ups\PickupPackage($accessKey, $userId, $password, TRUE);

    try
    {
        $res = $pickupFreight->pickupRequest("ProcessPickupCreation", processPickupCreation());
        var_dump(json_encode($res));
        echo '<br/><br/><br/>';

    } catch (Exception $ex) {
        echo "ex.......<br/>";
        var_dump(json_encode($ex));
        echo '<br/><br/><br/>';
    }

function processPickupCreation()
{
    //create soap request
    $requestoption['RequestOption'] = '1';
    $request['Request'] = $requestoption;
    $request['RatePickupIndicator'] = 'N';
    
    $account['AccountNumber']= 'W2864W';
    $account['AccountCountryCode'] = 'US';
    $shipper['Account'] = $account;
    $request['Shipper'] = $shipper;
    
    $pickupdateinfo['CloseTime'] = '1400';
    $pickupdateinfo['ReadyTime'] ='0500';
    $pickupdateinfo['PickupDate'] = '20100104';
    $request['PickupDateInfo'] = $pickupdateinfo;
    
    $pickupaddress['CompanyName'] = 'Pickup Proxy';
    $pickupaddress['ContactName'] = 'Pickup Manager';
    $pickupaddress['AddressLine'] = '315 Saddle Bridge Drive';
    $pickupaddress['Room'] = 'RO1';
    $pickupaddress['Floor'] = '2';
    $pickupaddress['City'] = 'Allendale';
    $pickupaddress['StateProvince'] = 'NJ';
    $pickupaddress['Urbanization'] = '';
    $pickupaddress['PostalCode'] = '07401';
    $pickupaddress['CountryCode'] = 'US';
    $pickupaddress['ResidentialIndicator'] = 'Y';
    $pickupaddress['PickupPoint'] = 'Lobby';
    $phone['Number'] = '6785851399';
    $phone['Extension'] = '911';
    $pickupaddress['Phone'] = $phone;
    $request['PickupAddress'] = $pickupaddress;
    
    $request['AlternateAddressIndicator'] = 'Y';
    
    $pickuppiece['ServiceCode'] = '001';
    $pickuppiece['Quantity'] = '27';
    $pickuppiece['DestinationCountryCode'] = 'US';
    $pickuppiece['ContainerCode'] = '01';
    $request['PickupPiece'] = $pickuppiece;
    
    $totalweight['Weight'] = '5.5';
    $totalweight['UnitOfMeasurement'] = 'LBS';
    $request['TotalWeight'] = $totalweight;
    
    $request['OverweightIndicator'] =  'N';
    $request['PaymentMethod'] = '01';
    $request['SpecialInstruction'] =  'Test';
    $request['ReferenceNumber'] = '';
    
    $cnfrmemailaddr =  array
    (
        'jdoe@ups.com',
        'edoe@ups.com'
    );
    $notification['ConfirmationEmailAddress'] = $cnfrmemailaddr;
    $notification['UndeliverableEmailAddress'] = '';
    $request['Notification'] = $notification;

    echo "Request.......<br/>";
    var_dump($request);
    echo "<br/><br/>";

    return $request;
}

