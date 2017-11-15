<?php

namespace AmericanExpress\HyperledgerFabricClient\Factory;

use Hyperledger\Fabric\Protos\MSP\SerializedIdentity;

class SerializedIdentityFactory
{
    /**
     * @param string $mspId
     * @param string $idBytes
     * @return SerializedIdentity
     */
    public static function fromBytes(string $mspId, string $idBytes): SerializedIdentity
    {
        $serializedIdentity = new SerializedIdentity();
        $serializedIdentity->setMspid($mspId);
        $serializedIdentity->setIdBytes($idBytes);

        return $serializedIdentity;
    }

    /**
     * @param string $mspID
     * @param \SplFileObject $file
     * @return SerializedIdentity
     */
    public static function fromFile(string $mspID, \SplFileObject $file): SerializedIdentity
    {
        $serializedIdentity = new SerializedIdentity();
        $serializedIdentity->setMspid($mspID);
        $serializedIdentity->setIdBytes($file->fread($file->getSize()));

        return $serializedIdentity;
    }
}
