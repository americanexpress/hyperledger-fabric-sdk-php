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

namespace AmericanExpressTest\HyperledgerFabricClient\Transaction;

use AmericanExpress\HyperledgerFabricClient\Nonce\RandomBytesNonceGenerator;
use AmericanExpress\HyperledgerFabricClient\Transaction\TransactionContext;
use AmericanExpress\HyperledgerFabricClient\Transaction\TransactionContextFactory;
use AmericanExpress\HyperledgerFabricClient\Transaction\TransactionRequest;
use AmericanExpress\HyperledgerFabricClient\Transaction\TransactionIdGenerator;
use AmericanExpress\HyperledgerFabricClient\Transaction\TxIdFactory;
use Hyperledger\Fabric\Protos\MSP\SerializedIdentity;
use PHPUnit\Framework\TestCase;

/**
 * @covers \AmericanExpress\HyperledgerFabricClient\Transaction\TransactionContextFactory
 */
class TransactionContextFactoryTest extends TestCase
{
    /**
     * @var TransactionContextFactory
     */
    private $sut;

    protected function setUp()
    {
        $this->sut = new TransactionContextFactory(
            new RandomBytesNonceGenerator(),
            new TransactionIdGenerator()
        );
    }

    public function testFromSerializedIdentity()
    {
        $result = $this->sut->fromSerializedIdentity(new SerializedIdentity());

        self::assertInstanceOf(TransactionContext::class, $result);
    }
}
