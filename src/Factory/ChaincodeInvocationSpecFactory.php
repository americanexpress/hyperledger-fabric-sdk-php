<?php
declare(strict_types=1);

namespace AmericanExpress\HyperledgerFabricClient\Factory;

use Google\Protobuf\Internal\RepeatedField;
use Hyperledger\Fabric\Protos\Peer\ChaincodeInvocationSpec;

class ChaincodeInvocationSpecFactory
{
    /**
     * @param string[]|RepeatedField $args
     * @return ChaincodeInvocationSpec
     */
    public static function fromArgs($args): ChaincodeInvocationSpec
    {
        $chaincodeSpec = ChaincodeSpecFactory::fromArgs($args);

        $chaincodeInvocationSpec = new ChaincodeInvocationSpec();
        $chaincodeInvocationSpec->setChaincodeSpec($chaincodeSpec);

        return $chaincodeInvocationSpec;
    }
}
