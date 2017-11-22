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

namespace AmericanExpress\HyperledgerFabricClient\Transaction;

use AmericanExpress\HyperledgerFabricClient\ChannelContext;
use AmericanExpress\HyperledgerFabricClient\ProtoFactory\SerializedIdentityFactory;
use AmericanExpress\HyperledgerFabricClient\Nonce\NonceGeneratorInterface;

final class TransactionContextFactory implements TransactionContextFactoryInterface
{
    /**
     * @var NonceGeneratorInterface
     */
    private $nonceGenerator;

    /**
     * @var TxIdFactoryInterface
     */
    private $txIdFactory;

    /**
     * @var int
     */
    private $epoch = 0;

    /**
     * @param NonceGeneratorInterface $nonceGenerator
     * @param TxIdFactoryInterface $txIdFactory
     * @param int $epoch
     */
    public function __construct(
        NonceGeneratorInterface $nonceGenerator,
        TxIdFactoryInterface $txIdFactory,
        int $epoch = 0
    ) {
        $this->nonceGenerator = $nonceGenerator;
        $this->txIdFactory = $txIdFactory;
        $this->epoch = $epoch;
    }

    /**
     * @param ChannelContext $channelContext
     * @return TransactionContext
     */
    public function fromChannelContext(ChannelContext $channelContext): TransactionContext
    {
        $identity = SerializedIdentityFactory::fromFile($channelContext->getMspId(), $channelContext->getAdminCerts());

        $nonce = $this->nonceGenerator->generateNonce();

        $txId = $this->txIdFactory->fromSerializedIdentity($identity, $nonce);

        return new TransactionContext($identity, $nonce, $txId, $this->epoch);
    }
}
