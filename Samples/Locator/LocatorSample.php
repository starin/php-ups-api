<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

include("../../tests/bootstrap.php");
use Ups;

$accessKey="Add Access Key Here";
$userId="Add UserName Here";
$password="Add Password Here";
$outputFileName="Response.xml";


   echo 'locate:<br/>';
    $locator = new Ups\Locator($accessKey, $userId, $password, true);
    try
    {
        $requestLocator = array(
            "Request"=>array(
                "RequestOption"=>1,
                "TransactionReference"=>array(
                "CustomerContext" => "Your Test Case Summary Description",
                "XpciVersion" => "1.0014"),
            ),

            "OriginAddress"=>array(
                'PhoneNumber'=> array("PhoneDialPlanNumber" => '',
                "PhoneLineNumber" => '',),
                "AddressKeyFormat"=>array(
                    "AddressLine" => "200 warsaw rd",
                    "PoliticalDivision2" => "Atlanta",
                    "PoliticalDivision1" => "GA",
                    "PostcodePrimaryLow" => "85281",
                    "PostcodeExtendedLow" => "4510",
                    "CountryCode" => "US"
                ),
                "MaximumListSize" => '',
            ),
            "Translate"=>array("LanguageCode" => "ENG",),
            "UnitOfMeasurement"=>array("Code" => "KM",),
            "LocationID" => ''
        );
        $res = $locator->locate($requestLocator);

        var_dump(json_encode($res));
        echo '<br/><br/><br/>';

    } catch (Exception $exc) {
   
        var_dump($exc);
    }

