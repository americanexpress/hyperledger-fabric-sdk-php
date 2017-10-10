<?php

namespace AmericanExpress\FabricClient\msp;
class Identity
{

    function createSerializedIdentity($certs, $mspID)
    {
        $currDirectory = ROOTPATH . $certs;

        //echo $currDirectory;

//        die();

        $data = file_get_contents($currDirectory);
        //echo $data; die();
        $SerializedIdentity = new \Msp\SerializedIdentity();
        $SerializedIdentity->setMspid($mspID);
        $SerializedIdentity->setIdBytes($data);


        return $SerializedIdentity;
    }
}