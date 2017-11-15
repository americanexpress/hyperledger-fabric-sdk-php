<?php
declare(strict_types=1);

namespace AmericanExpress\HyperledgerFabricClient\Cryptography;

use Hyperledger\Fabric\Protos\MSP\SerializedIdentity;
use Hyperledger\Fabric\Protos\Peer\Proposal;

interface CryptographyInterface
{
    /**
     * @param Proposal $proposal
     * @param \SplFileObject $privateKey
     * @return string
     */
    public function signByteString(Proposal $proposal, \SplFileObject $privateKey): string;

    /**
     * Function for getting random nonce value
     *  random number(nonce) which in turn used to generate txId.
     */
    public function getNonce(): string;

    /**
     * @param SerializedIdentity $serializedIdentity
     * @param string $nonce
     * @return string
     */
    public function createTxId(SerializedIdentity $serializedIdentity, string $nonce): string;
}
