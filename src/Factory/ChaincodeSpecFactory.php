<?php
declare(strict_types=1);

namespace AmericanExpress\HyperledgerFabricClient\Factory;

use Google\Protobuf\Internal\RepeatedField;
use Hyperledger\Fabric\Protos\Peer\ChaincodeSpec;

class ChaincodeSpecFactory
{
    /**
     * @param string[]|RepeatedField $args
     * @return ChaincodeSpec
     */
    public static function fromArgs($args): ChaincodeSpec
    {
        $chaincodeInput = ChaincodeInputFactory::fromArgs($args);

        $chaincodeSpec = new ChaincodeSpec();
        $chaincodeSpec->setInput($chaincodeInput);

        return $chaincodeSpec;
    }
}
