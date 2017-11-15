<?php
declare(strict_types=1);

namespace AmericanExpress\HyperledgerFabricClient\Factory;

use Hyperledger\Fabric\Protos\Peer\ChaincodeHeaderExtension;
use Hyperledger\Fabric\Protos\Peer\ChaincodeID;

class ChaincodeHeaderExtensionFactory
{
    /**
     * @param ChaincodeID $chaincodeId
     * @return ChaincodeHeaderExtension
     */
    public static function fromChaincodeId(ChaincodeID $chaincodeId): ChaincodeHeaderExtension
    {
        $chaincodeHeaderExtension = new ChaincodeHeaderExtension();
        $chaincodeHeaderExtension->setChaincodeId($chaincodeId);

        return $chaincodeHeaderExtension;
    }
}
