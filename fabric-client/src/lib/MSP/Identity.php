<?php
declare(strict_types=1);

namespace AmericanExpress\HyperledgerFabricClient\MSP;

use Hyperledger\Fabric\Protos\MSP as Msp;

class Identity
{
    /**
     * @param string $certs
     * @param string $mspID
     * @return Msp\SerializedIdentity
     */
    public function createSerializedIdentity(string $certs, string $mspID): Msp\SerializedIdentity
    {
        $currDirectory = ROOTPATH . $certs;
        $data = file_get_contents($currDirectory);
        $serializedIdentity = new Msp\SerializedIdentity();
        $serializedIdentity->setMspid($mspID);
        $serializedIdentity->setIdBytes($data);

        return $serializedIdentity;
    }
}
