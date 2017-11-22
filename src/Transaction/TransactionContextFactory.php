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

use AmericanExpress\HyperledgerFabricClient\ProtoFactory\SerializedIdentityFactory;
use AmericanExpress\HyperledgerFabricClient\Nonce\NonceGeneratorInterface;

final class TransactionContextFactory implements TransactionContextFactoryInterface
{
    /**
     * @var NonceGeneratorInterface
     */
    private $nonceGenerator;

    /**
     * @var TransactionIdGeneratorInterface
     */
    private $transactionIdGenerator;

    /**
     * @var int
     */
    private $epoch;

    /**
     * @param NonceGeneratorInterface $nonceGenerator
     * @param TransactionIdGeneratorInterface $transactionIdGenerator
     * @param int $epoch
     */
    public function __construct(
        NonceGeneratorInterface $nonceGenerator,
        TransactionIdGeneratorInterface $transactionIdGenerator,
        int $epoch = 0
    ) {
        $this->nonceGenerator = $nonceGenerator;
        $this->transactionIdGenerator = $transactionIdGenerator;
        $this->epoch = $epoch;
    }

    /**
     * @param TransactionRequest $request
     * @return TransactionContext
     */
    public function fromTransactionRequest(TransactionRequest $request): TransactionContext
    {
        $identity = SerializedIdentityFactory::fromFile(
            $request->getOrganization()->getMspId(),
            new \SplFileObject($request->getOrganization()->getAdminCerts())
        );

        $nonce = $this->nonceGenerator->generateNonce();

        $txId = $this->transactionIdGenerator->fromSerializedIdentity($identity, $nonce);

        return new TransactionContext($identity, $nonce, $txId, $this->epoch);
    }
}
