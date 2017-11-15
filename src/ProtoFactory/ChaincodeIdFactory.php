<?php
declare(strict_types=1);

namespace AmericanExpress\HyperledgerFabricClient\ProtoFactory;

use Hyperledger\Fabric\Protos\Peer\ChaincodeID;

class ChaincodeIdFactory
{
    /**
     * @param string $path
     * @param string $name
     * @param string $version
     * @return ChaincodeID
     */
    public static function create(string $path, string $name, string $version): ChaincodeID
    {
        $chainCodeId = new ChaincodeID();
        $chainCodeId->setPath($path);
        $chainCodeId->setName($name);
        $chainCodeId->setVersion($version);

        return $chainCodeId;
    }
}
