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
        var_dump($activity);
    }
} catch (Exception $e) {
    var_dump($e);
}

echo "Done";
