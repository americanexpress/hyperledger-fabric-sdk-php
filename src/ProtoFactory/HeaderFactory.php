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
use Hyperledger\Fabric\Protos\Common\ChannelHeader;
use Hyperledger\Fabric\Protos\Common\Header;
use Hyperledger\Fabric\Protos\Common\SignatureHeader;
use Hyperledger\Fabric\Protos\MSP\SerializedIdentity;

class HeaderFactory
{
    /**
     * @param ChannelHeader $channelHeader
     * @param SignatureHeader $signatureHeader
     * @return Header
     */
    public static function create(
        ChannelHeader $channelHeader,
        SignatureHeader $signatureHeader
    ): Header {
        $header = new Header();
        $header->setChannelHeader($channelHeader->serializeToString());
        $header->setSignatureHeader($signatureHeader->serializeToString());

        return $header;
    }

    /**
     * @param ChannelHeader $channelHeader
     * @param SerializedIdentity $serializedIdentity
     * @param string $nonce
     * @return Header
     */
    public static function createFromSerializedIdentity(
        ChannelHeader $channelHeader,
        SerializedIdentity $serializedIdentity,
        string $nonce
    ): Header {
        $signatureHeader = SignatureHeaderFactory::create($serializedIdentity, $nonce);

        return self::create($channelHeader, $signatureHeader);
    }

    /**
     * @param ChannelHeader $channelHeader
     * @param TransactionContext $transactionContext
     * @return Header
     */
    public static function fromTransactionContext(
        ChannelHeader $channelHeader,
        TransactionContext $transactionContext
    ): Header {
        return self::createFromSerializedIdentity(
            $channelHeader,
            $transactionContext->getSerializedIdentity(),
            $transactionContext->getNonce()
        );
    }
}
