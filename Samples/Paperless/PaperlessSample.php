<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

include("../../tests/bootstrap.php");

use Ups;
$accessKey="Add Access Key Here";
$userId="Add UserName Here";
$password="Add Password Here";

$paperless = new Ups\Paperless($accessKey, $userId, $password, TRUE);
try {
   
    $paperless->setCustomerContext("Upload Request");
    $paperless->setTransactionIdentifier("String");
    $paperless->setShipperNumber("");
    $paperless->setFileName("Sample Test File");
    $paperless->setFileText("Tm90aWNlDQpJbiBhbGwgY29tbXVuaWNhdGlvbnMgd2l0aCBVUFMgY29uY2VybmluZyB0aGlzIGRvY3VtZW50LCBwbGVhc2UgcmVmZXIgdG8gdGhlIGRvY3VtZW50IGRhdGUgbG9jYXRlZCBvbiB0aGUgY292ZXIuDQpDb3B5cmlnaHQNClRoZSB1c2UsIGRpc2Nsb3N1cmUsIHJlcHJvZHVjdGlvbiwgbW9kaWZpY2F0aW9uLCB0cmFuc2Zlciwgb3IgdHJhbnNtaXR0YWwgb2YgdGhpcyB3b3JrIGZvciBhbnkgcHVycG9zZSBpbiBhbnkgZm9ybSBvciBieSBhbnkgbWVhbnMgd2l0aG91dCB0aGUgd3JpdHRlbiBwZXJtaXNzaW9uIG9mIFVuaXRlZCBQYXJjZWwgU2VydmljZSBpcyBzdHJpY3RseSBwcm9oaWJpdGVkLg0KqSBDb3B5cmlnaHQgMjAxMiBVbml0ZWQgUGFyY2VsIFNlcnZpY2Ugb2YgQW1lcmljYSwgSW5jLiBBbGwgUmlnaHRzIFJlc2VydmVkLg0KVHJhZGVtYXJrcw0KVVBTIE9uTGluZa4gaXMgYSByZWdpc3RlcmVkIHRyYWRlbWFyayBvZiBVbml0ZWQgUGFyY2VsIFNlcnZpY2Ugb2YgQW1lcmljYSwgSW5jLiBBbGwgb3RoZXIgdHJhZGVtYXJrcyBhcmUgdGhlIHByb3BlcnR5IG9mIHRoZWlyIHJlc3BlY3RpdmUgb3duZXJzLg0KU29tZSBvZiB0aGUgVVBTIGNvcnBvcmF0ZSBhcHBsaWNhdGlvbnMgdXNlIFUuUy4gY2l0eSwgc3RhdGUsIGFuZCBwb3N0YWwgY29kZSBpbmZvcm1hdGlvbiBvYnRhaW5lZCBieSBVbml0ZWQgUGFyY2VsIFNlcnZpY2Ugb2YgQW1lcmljYSwgSW5jLiB1bmRlciBhIG5vbi1leGNsdXNpdmUgbGljZW5zZSBmcm9tIHRoZSBVbml0ZWQgU3RhdGVzIFBvc3RhbCBTZXJ2aWNlLiANCg==");
    $paperless->setFileFormat("txt");
    $paperless->setFileDocumentType("011");
  $resp = $paperless->PaperLessRequest("processUploading");    
  var_dump($resp);
    
} catch (Exception $e) {
    var_dump($e);
}

echo "Done";
