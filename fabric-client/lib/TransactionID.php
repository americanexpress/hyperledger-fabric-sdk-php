<?php

namespace fabric\sdk;
class TransactionID
{
    static $config = null;

    public function getTxId($nounce, $org)
    {

        self::$config = \Config::getOrgConfig($org);

        $identity = new Identity();
        $identity = $identity->createSerializedIdentity(self::$config["admin_certs"], self::$config["mspid"]);
        $identitystring = $identity->serializeToString();

        $utils = new Utils();
        $noArray = $utils->toByteArray($nounce);
        $identtyArray = $utils->toByteArray($identitystring);
        $comp = array_merge($noArray, $identtyArray);
        $compString = $utils->proposalArrayToBinaryString($comp);

        $txID = hash('sha256', $compString);

        return $txID;
    }
}