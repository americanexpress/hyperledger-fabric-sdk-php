<?php
declare(strict_types=1);

namespace AmericanExpress\HyperledgerFabricClient;

use Google\Protobuf\Internal\RepeatedField;
use Hyperledger\Fabric\Protos\Peer\ChaincodeInput;
use Hyperledger\Fabric\Protos\Peer\ChaincodeInvocationSpec;
use Hyperledger\Fabric\Protos\Peer\ChaincodeSpec;

class ChaincodeInvocationSpecFactory
{
    /**
     * @param string[]|RepeatedField $args
     * @return ChaincodeInvocationSpec specify parameters of chaincode to be invoked during transaction.
     * specify parameters of chaincode to be invoked during transaction.
     */
    public static function fromArgs($args): ChaincodeInvocationSpec
    {
        $chaincodeInput = new ChaincodeInput();
        $chaincodeInput->setArgs($args);

        $chaincodeSpec = new ChaincodeSpec();
        $chaincodeSpec->setInput($chaincodeInput);

        $chaincodeInvocationSpec = new ChaincodeInvocationSpec();
        $chaincodeInvocationSpec->setChaincodeSpec($chaincodeSpec);

        return $chaincodeInvocationSpec;
    }
}
