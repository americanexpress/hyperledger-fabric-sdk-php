<?php
namespace org\amex\fabric_client;
class Identity
{

    function createSerializedIdentity($certs, $mspID)
    {
        $data = file_get_contents($certs);

        $SerializedIdentity = new \Msp\SerializedIdentity();
        $SerializedIdentity->setMspid($mspID);
        $SerializedIdentity->setIdBytes($data);

        return $SerializedIdentity;
    }
}