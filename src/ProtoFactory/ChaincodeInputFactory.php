<?php
declare(strict_types=1);

namespace AmericanExpress\HyperledgerFabricClient\ProtoFactory;

use Google\Protobuf\Internal\RepeatedField;
use Hyperledger\Fabric\Protos\Peer\ChaincodeInput;

class ChaincodeInputFactory
{
    /**
     * @param string[]|RepeatedField $args
     * @return ChaincodeInput
     */
    public static function fromArgs($args): ChaincodeInput
    {
        $chaincodeInput = new ChaincodeInput();
        $chaincodeInput->setArgs($args);

        return $chaincodeInput;
    }
}
