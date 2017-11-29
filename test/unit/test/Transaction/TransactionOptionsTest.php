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
use AmericanExpress\HyperledgerFabricClient\Peer\PeerOptionsInterface;
use AmericanExpress\HyperledgerFabricClient\Transaction\TransactionOptions;
use PHPUnit\Framework\TestCase;

/**
 * @covers \AmericanExpress\HyperledgerFabricClient\Transaction\TransactionOptions
 */
class TransactionOptionsTest extends TestCase
{
    /**
     * @var TransactionOptions
     */
    private $sut;

    protected function setUp()
    {
        $this->sut = new TransactionOptions();
    }

    public function testDefaultPeers()
    {
        self::assertFalse($this->sut->hasPeers());
        self::assertCount(0, $this->sut->getPeers());
    }

    public function testAddOnePeer()
    {
        self::assertFalse($this->sut->hasPeers());
        self::assertCount(0, $this->sut->getPeers());

        $peer = new PeerOptions([
            'name' => 'peer1',
        ]);

        $this->sut->addPeers($peer);

        self::assertTrue($this->sut->hasPeers());
        self::assertCount(1, $this->sut->getPeers());
        self::assertSame([$peer], $this->sut->getPeers());
    }

    public function testFromArray()
    {
        $peer = new PeerOptions([
            'name' => 'peer1',
        ]);

        $sut = new TransactionOptions([
            'peers' => [$peer],
        ]);

        self::assertSame([$peer], $sut->getPeers());
    }

    public function testFromMultiDimensionalArray()
    {
        $sut = new TransactionOptions([
            'peers' => [
                [
                    'name' => 'peer1',
                ]
            ],
        ]);

        self::assertCount(1, $sut->getPeers());
        self::assertInstanceOf(PeerOptionsInterface::class, $sut->getPeers()[0]);
        self::assertSame('peer1', $sut->getPeers()[0]->getName());
    }

    public function testSetPeers()
    {
        $peer = new PeerOptions([
            'name' => 'peer1',
        ]);

        $this->sut->setPeers([$peer]);

        self::assertSame([$peer], $this->sut->getPeers());
    }

    public function testSetPeersFromArray()
    {
        $this->sut->setPeers([
            [
                'name' => 'peer1',
            ]
        ]);

        self::assertCount(1, $this->sut->getPeers());
        self::assertInstanceOf(PeerOptionsInterface::class, $this->sut->getPeers()[0]);
        self::assertSame('peer1', $this->sut->getPeers()[0]->getName());
    }

    public function testAddManyPeers()
    {
        $this->sut->addPeers(
            new PeerOptions([
                'name' => 'peer1',
            ]),
            new PeerOptions([
                'name' => 'peer2',
            ]),
            new PeerOptions([
                'name' => 'peer3',
            ])
        );

        self::assertCount(3, $this->sut->getPeers());
    }
}
