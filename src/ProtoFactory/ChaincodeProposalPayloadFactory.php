<?php
declare(strict_types=1);

namespace AmericanExpress\HyperledgerFabricClient\ProtoFactory;

use Hyperledger\Fabric\Protos\Peer\ChaincodeInvocationSpec;
use Hyperledger\Fabric\Protos\Peer\ChaincodeProposalPayload;

class ChaincodeProposalPayloadFactory
{
    /**
     * @param ChaincodeInvocationSpec $chaincodeInvocationSpec
     * @return ChaincodeProposalPayload
     */
    public static function fromChaincodeInvocationSpec(
        ChaincodeInvocationSpec $chaincodeInvocationSpec
    ): ChaincodeProposalPayload {
        $chaincodeProposalPayload = new ChaincodeProposalPayload();
        $chaincodeProposalPayload->setInput($chaincodeInvocationSpec->serializeToString());

        return $chaincodeProposalPayload;
    }
}
