<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);

include("../../tests/bootstrap.php");

use Ups;
$accessKey="Add Access Key Here";
$userId="Add UserName Here";
$password="Add Password Here";
  echo 'track:<br/>';
    $tracking = new Ups\Tracking($accessKey, $userId, $password, true);
    try {
        $shipment = $tracking->track('1Z14709E0311522012');
      
        foreach($shipment->Package->Activity as $activity) {
            
            var_dump(json_encode($activity));
            echo '<br/><br/><br/>';
        }
        
              
            
           
    } catch (Exception $ex) {
        var_dump($ex);
    }
