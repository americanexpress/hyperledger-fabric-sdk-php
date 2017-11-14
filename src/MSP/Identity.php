<?php
declare(strict_types=1);

namespace AmericanExpress\HyperledgerFabricClient\MSP;

use Hyperledger\Fabric\Protos\MSP\SerializedIdentity;

class Identity
{
    /**
     * @param string $certs
     * @param string $mspID
     * @return SerializedIdentity
     */
    public function createSerializedIdentity(string $certs, string $mspID): SerializedIdentity
    {
        $data = \file_get_contents($certs);
        $serializedIdentity = new SerializedIdentity();
        $serializedIdentity->setMspid($mspID);
        $serializedIdentity->setIdBytes($data);

        return $serializedIdentity;
    }
}
