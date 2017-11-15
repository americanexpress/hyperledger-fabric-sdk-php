<?php
declare(strict_types=1);

namespace AmericanExpress\HyperledgerFabricClient\ProtoFactory;

use Hyperledger\Fabric\Protos\Common\ChannelHeader;
use Hyperledger\Fabric\Protos\Common\Header;
use Hyperledger\Fabric\Protos\MSP\SerializedIdentity;

class HeaderFactory
{
    /**
     * @param SerializedIdentity $serializedIdentity
     * @param ChannelHeader $channelHeader
     * @param string $nonce
     * @return Header
     */
    public static function create(
        SerializedIdentity $serializedIdentity,
        ChannelHeader $channelHeader,
        string $nonce
    ): Header {
        $signatureHeader = SignatureHeaderFactory::create($serializedIdentity, $nonce);

        $header = new Header();
        $header->setChannelHeader($channelHeader->serializeToString());
        $header->setSignatureHeader($signatureHeader->serializeToString());

        return $header;
    }
}
