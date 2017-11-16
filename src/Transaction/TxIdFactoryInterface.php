<?php
declare(strict_types=1);

namespace AmericanExpress\HyperledgerFabricClient\Transaction;

use Hyperledger\Fabric\Protos\MSP\SerializedIdentity;

interface TxIdFactoryInterface
{
    /**
     * @param SerializedIdentity $serializedIdentity
     * @param string $nonce
     * @return string
     */
    public function fromSerializedIdentity(SerializedIdentity $serializedIdentity, string $nonce): string;
}
