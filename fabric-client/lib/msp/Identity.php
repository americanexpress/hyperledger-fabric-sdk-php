<?php

namespace fabric\sdk;
class Identity
{

    function createSerializedIdentity($certs, $mspID)
    {
        $currDirectory = __DIR__ . "/../../../" . $certs;

        $data = file_get_contents($currDirectory);

        $SerializedIdentity = new \Msp\SerializedIdentity();
        $SerializedIdentity->setMspid($mspID);
        $SerializedIdentity->setIdBytes($data);


        return $SerializedIdentity;
    }
}