<?php
declare(strict_types=1);

namespace AmericanExpress\HyperledgerFabricClient\Factory;

use Hyperledger\Fabric\Protos\Common\SignatureHeader;
use Hyperledger\Fabric\Protos\MSP\SerializedIdentity;

class SignatureHeaderFactory
{
    /**
     * @param SerializedIdentity $serializedIdentity
     * @param string $nonce
     * @return SignatureHeader
     */
    public static function create(SerializedIdentity $serializedIdentity, string $nonce): SignatureHeader
    {
        $signatureHeader = new SignatureHeader();
        $signatureHeader->setCreator($serializedIdentity->serializeToString());
        $signatureHeader->setNonce($nonce);

        return $signatureHeader;
    }
}
