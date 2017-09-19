<?php

namespace org\amex\fabric_client;
class Identity
{

    function createSerializedIdentity($certs, $mspID)
    {
        $currDirectory = __DIR__ . "/../../../test/fixtures/" . $certs;
        $data = file_get_contents($currDirectory);

        $SerializedIdentity = new \Msp\SerializedIdentity();
        $SerializedIdentity->setMspid($mspID);
        $SerializedIdentity->setIdBytes($data);


        return $SerializedIdentity;
    }
}