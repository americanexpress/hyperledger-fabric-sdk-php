<?php
namespace org\amex\fabric_client;
class TransactionID{

    public function getTxId($nounce)
    {
        $ADMIN_CERTS = "resources/Admin@org1.example.com-cert.pem";
        $fixmspID = "Org1MSP";
        $identity = new Identity();
        $identity =  $identity->createSerializedIdentity($ADMIN_CERTS, $fixmspID);
        $identitystring = $identity->serializeToString();

        $utils = new Utils();
        $noArray = $utils->toByteArray($nounce);
        $identtyArray = $utils->toByteArray($identitystring);
        $comp = array_merge($noArray, $identtyArray);
        $compString = $utils->arrayToBinaryString($comp);

        $txID = hash('sha256', $compString);

        return $txID;
    }
}