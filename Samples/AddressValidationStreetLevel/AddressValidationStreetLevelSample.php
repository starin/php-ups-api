<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

include("../../tests/bootstrap.php");
use Ups;

$accessKey="Add Access Key Here";
$userId="Add UserName Here";
$password="Add Password Here";
    echo 'validateAddressSL:<br/>';
    $addressValidationSL = new Ups\AddressValidationStreetLevel($accessKey, $userId, $password, true);
    try {
        $avslOpt = array(
            'TransactionReference' => array(
                'CustomerContext' => 'Customer Data',
            ),
            'RequestOption' => '3',
            'AddressKeyFormat' => array(
                'AddressLine' => 'AIRWAY ROAD SUITE 7',
                'PoliticalDivision2' => 'SAN DIEGO',
                'PoliticalDivision1' => 'CA',
                'PostcodePrimaryLow' => '92154',
                'CountryCode' => 'US',
            )
        );
        $xadValid = $addressValidationSL->validate($avslOpt);
        var_dump(json_encode($xadValid));
        echo '<br/><br/><br/>';
        
    } catch (Exception $ex) {
        var_dump($ex);
    }

