<?php
declare(strict_types=1);

namespace AmericanExpress\HyperledgerFabricClient\ProtoFactory;

use Google\Protobuf\Timestamp;
use Hyperledger\Fabric\Protos\Common\ChannelHeader;

class ChannelHeaderFactory
{
    private const DEFAULT_CHANNEL_HEADER_TYPE = 3;

    /**
     * @param string $txId
     * @param string $channelId
     * @param string $chaincodePath
     * @param string $chaincodeName
     * @param string $chaincodeVersion
     * @param int|string $epoch
     * @param int $type
     * @param int $version
     * @param Timestamp $timestamp
     * @return ChannelHeader
     */
    public static function create(
        string $txId,
        string $channelId,
        string $chaincodePath,
        string $chaincodeName,
        string $chaincodeVersion,
        $epoch = 0,
        int $type = self::DEFAULT_CHANNEL_HEADER_TYPE,
        int $version = 1,
        Timestamp $timestamp = null
    ): ChannelHeader {
        $timestamp = $timestamp ?: TimestampFactory::fromDateTime();

        $chaincodeId = ChaincodeIdFactory::create($chaincodePath, $chaincodeName, $chaincodeVersion);

        $chaincodeHeaderExtension = ChaincodeHeaderExtensionFactory::fromChaincodeId($chaincodeId);

        $channelHeader = new ChannelHeader();
        $channelHeader->setType($type);
        $channelHeader->setVersion($version);
        $channelHeader->setTxId($txId);
        $channelHeader->setChannelId($channelId);
        $channelHeader->setEpoch($epoch);
        $channelHeader->setTimestamp($timestamp);
        $channelHeader->setExtension($chaincodeHeaderExtension->serializeToString());

        return $channelHeader;
    }
}
