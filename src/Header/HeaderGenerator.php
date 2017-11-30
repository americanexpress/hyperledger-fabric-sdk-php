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

namespace AmericanExpress\HyperledgerFabricClient\Header;

use AmericanExpress\HyperledgerFabricClient\ProtoFactory\HeaderFactory;
use AmericanExpress\HyperledgerFabricClient\ProtoFactory\SignatureHeaderFactory;
use AmericanExpress\HyperledgerFabricClient\Transaction\TransactionIdentifier;
use AmericanExpress\HyperledgerFabricClient\Transaction\TransactionIdentifierGeneratorInterface;
use Hyperledger\Fabric\Protos\Common\ChannelHeader;
use Hyperledger\Fabric\Protos\Common\Header;
use Hyperledger\Fabric\Protos\Common\SignatureHeader;
use Hyperledger\Fabric\Protos\MSP\SerializedIdentity;

final class HeaderGenerator implements HeaderGeneratorInterface
{
    /**
     * @var TransactionIdentifierGeneratorInterface
     */
    private $transactionIdentifierGenerator;

    /**
     * @var int
     */
    private $epoch;

    /**
     * HeaderGenerator constructor.
     * @param TransactionIdentifierGeneratorInterface $transactionIdentifierGenerator
     * @param int $epoch
     */
    public function __construct(TransactionIdentifierGeneratorInterface $transactionIdentifierGenerator, $epoch = 0)
    {
        $this->transactionIdentifierGenerator = $transactionIdentifierGenerator;
        $this->epoch = $epoch;
    }

    /**
     * @param SerializedIdentity $identity
     * @param TransactionIdentifier $transactionIdentifier
     * @return SignatureHeader
     */
    private function generateSignatureHeader(
        SerializedIdentity $identity,
        TransactionIdentifier $transactionIdentifier
    ): SignatureHeader {
        return SignatureHeaderFactory::create(
            $identity,
            $transactionIdentifier->getNonce()
        );
    }

    /**
     * @param SerializedIdentity $identity
     * @param ChannelHeader $channelHeader
     * @return Header
     */
    public function fromChannelHeader(SerializedIdentity $identity, ChannelHeader $channelHeader): Header
    {
        $transactionId = $this->transactionIdentifierGenerator->fromSerializedIdentity($identity);
        $channelHeader->setTxId($transactionId->getId());
        $channelHeader->setEpoch($this->epoch);
        return HeaderFactory::create($this->generateSignatureHeader($identity, $transactionId), $channelHeader);
    }
}
