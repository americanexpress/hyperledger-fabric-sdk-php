<?php

namespace fabric\sdk;
class TransactionID
{

    public function getTxId($nounce)
    {
        $member = \Config::getConfig("members");

        $ADMIN_CERTS = $member[0]->admin_certs;
        $fixmspID = "Org1MSP";
        $identity = new Identity();
        $identity = $identity->createSerializedIdentity($ADMIN_CERTS, $fixmspID);
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