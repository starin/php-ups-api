<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

include("../../tests/bootstrap.php");
use Ups;

$accessKey="Add Access Key Here";
$userId="Add UserName Here";
$password="Add Password Here";
 echo 'testShipmentRate:<br/>';
    $rate = new Ups\Rate($accessKey, $userId, $password, true);
    try
    {
                
        $shipment = new Ups\Entity\Shipment();

        $shipment->Shipper = new Ups\Entity\Shipper();
        $shipment->Shipper->Name = 'Test Shipper';
        $shipment->Shipper->ShipperNumber = '12345';
        $shipment->Shipper->Address = new Ups\Entity\Address();
        $shipment->Shipper->Address->AddressLine1 = '123 Some St.';
        $shipment->Shipper->Address->City = 'Test';
        $shipment->Shipper->Address->PostalCode = '12345';
        $shipment->Shipper->Address->StateProvinceCode = 'WA';
        $shipment->ShipTo = new Ups\Entity\ShipTo();
        $shipment->ShipTo->CompanyName = 'Company Name';
        $shipment->ShipTo->Address = new Ups\Entity\Address();
        $shipment->ShipTo->Address->AddressLine1 = '1234 Some St.';
        $shipment->ShipTo->Address->City = 'Corado';
        $shipment->ShipTo->Address->PostalCode = '00646';
        $shipment->ShipTo->Address->StateProvinceCode = 'PR';

        $shipment->Service = new Ups\Entity\Service();
        $shipment->Service->Code = '03';


        $package = new Ups\Entity\Package();

        $package->PackagingType = new Ups\Entity\PackagingType();
        $package->PackagingType->Code = '02';

        $package->PackageWeight = new Ups\Entity\PackageWeight();
        $package->PackageWeight->Weight = '10';
        $shipment->Package = array(
            $package,
        );

        // should throw exception cause invalid zip code
        $response = $rate->getRate($shipment);
        
      var_dump(json_encode($res));
        echo '<br/><br/><br/>';
    }
    catch (Exception $ex) {
        var_dump($ex);
    }
    
