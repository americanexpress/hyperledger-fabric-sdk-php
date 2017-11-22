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

    /**
     * @param TransactionContext $transactionContext
     * @param ChannelHeader $channelHeader
     * @return Header
     */
    public static function fromTransactionContext(
        TransactionContext $transactionContext,
        ChannelHeader $channelHeader
    ): Header {
        return self::create(
            $transactionContext->getSerializedIdentity(),
            $channelHeader,
            $transactionContext->getNonce()
        );
    }
}
