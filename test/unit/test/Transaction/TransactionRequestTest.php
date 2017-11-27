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

use AmericanExpress\HyperledgerFabricClient\Peer\PeerOptions;
use AmericanExpress\HyperledgerFabricClient\Transaction\TransactionRequest;
use PHPUnit\Framework\TestCase;

/**
 * @covers \AmericanExpress\HyperledgerFabricClient\Transaction\TransactionRequest
 */
class TransactionRequestTest extends TestCase
{
    /**
     * @var TransactionRequest
     */
    private $sut;

    protected function setUp()
    {
        $this->sut = new TransactionRequest();
    }

    public function testPeer()
    {
        self::assertFalse($this->sut->hasPeer());
        self::assertNull($this->sut->getPeer());

        $peer = new PeerOptions([
            'name' => 'peer1',
        ]);

        $this->sut->setPeer($peer);

        self::assertTrue($this->sut->hasPeer());
        self::assertSame($peer, $this->sut->getPeer());
    }

    public function testFromArray()
    {
        $peer = new PeerOptions([
            'name' => 'peer1',
        ]);

        $sut = new TransactionRequest([
            'peer' => $peer,
        ]);

        self::assertSame($peer, $sut->getPeer());
    }
}
