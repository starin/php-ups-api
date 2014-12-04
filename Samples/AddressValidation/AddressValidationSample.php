<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

include("../../tests/bootstrap.php");

use Ups;
$accessKey="Add Access Key Here";
$userId="Add UserName Here";
$password="Add Password Here";
  echo 'validateAddress:<br/>';
    $addressValidation = new Ups\AddressValidation($accessKey, $userId, $password, true);
    try {
        $requestOption = array(
            'Address' => array(
                'City' => 'ALPHARETTA',
                'PostalCode' => '300053778',
            ),
        );
        $adValid = $addressValidation->validate($requestOption);
        var_dump(json_encode($adValid));
        echo '<br/><br/><br/>';

    } catch (Exception $ex) {
        var_dump($ex);
    }