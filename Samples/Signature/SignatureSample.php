<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

include("../../tests/bootstrap.php");

use Ups;
$accessKey="Add Access Key Here";
$userId="Add UserName Here";
$password="Add Password Here";

$tracking = new Ups\Tracking($accessKey, $userId, $password, FALSE);
try {
      $shipment = $tracking->track('1Z14709E0311522012','Signature Image');
      

    foreach($shipment->Package->Activity as $activity) {
        var_dump(json_encode($activity));
    }
    
    $val = null;
    if($shipment->ReferenceNumber && $shipment->ReferenceNumber->Code && $shipment->ReferenceNumber->Code == "57") {
        $val = "";
        if($shipment->ReferenceNumber->Value) {
            $val = $shipment->ReferenceNumber->Value;
        }
    }
    
    var_dump("Bill of Lading Number: ");
    var_dump($val);
} catch (Exception $e) {
    var_dump(json_encode($e));
}

echo "Done";
