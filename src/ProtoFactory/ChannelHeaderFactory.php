<?php

/**
 * Copyright 2017 American Express Travel Related Services Company, Inc.
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 * http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express
 * or implied. See the License for the specific language governing
 * permissions and limitations under the License.
 */

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
