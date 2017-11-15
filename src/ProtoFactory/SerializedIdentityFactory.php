<?php

namespace AmericanExpress\HyperledgerFabricClient\ProtoFactory;

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
     * @param string $mspId
     * @param \SplFileObject $file
     * @return SerializedIdentity
     */
    public static function fromFile(string $mspId, \SplFileObject $file): SerializedIdentity
    {
        return self::fromBytes($mspId, $file->fread($file->getSize()));
    }
}
