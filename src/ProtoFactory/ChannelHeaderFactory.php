<?php
declare(strict_types=1);

namespace AmericanExpress\HyperledgerFabricClient\ProtoFactory;

use AmericanExpress\HyperledgerFabricClient\Transaction\TransactionContext;
use Google\Protobuf\Timestamp;
use Hyperledger\Fabric\Protos\Common\ChannelHeader;

class ChannelHeaderFactory
{
    private const DEFAULT_CHANNEL_HEADER_TYPE = 3;

    /**
     * @param TransactionContext $transactionContext
     * @param string $channelId
     * @param string $chaincodePath
     * @param string $chaincodeName
     * @param string $chaincodeVersion
     * @param int $type
     * @param int $version
     * @param Timestamp $timestamp
     * @return ChannelHeader
     */
    public static function create(
        TransactionContext $transactionContext,
        string $channelId,
        string $chaincodePath,
        string $chaincodeName,
        string $chaincodeVersion,
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
        $channelHeader->setTxId($transactionContext->getTxId());
        $channelHeader->setChannelId($channelId);
        $channelHeader->setEpoch($transactionContext->getEpoch());
        $channelHeader->setTimestamp($timestamp);
        $channelHeader->setExtension($chaincodeHeaderExtension->serializeToString());

        return $channelHeader;
    }
}
