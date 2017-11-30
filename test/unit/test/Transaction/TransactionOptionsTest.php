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

use AmericanExpress\HyperledgerFabricClient\Peer\PeerInterface;
use AmericanExpress\HyperledgerFabricClient\Peer\PeerOptions;
use AmericanExpress\HyperledgerFabricClient\Transaction\TransactionOptions;
use PHPUnit\Framework\TestCase;

/**
 * @covers \AmericanExpress\HyperledgerFabricClient\Transaction\TransactionOptions
 */
class TransactionOptionsTest extends TestCase
{
    /**
     * @var PeerInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $peer;

    /**
     * @var TransactionOptions
     */
    private $sut;

    protected function setUp()
    {
        $this->peer = $this->getMockBuilder(PeerInterface::class)
            ->getMock();

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

        $this->sut->addPeers($this->peer);

        self::assertTrue($this->sut->hasPeers());
        self::assertCount(1, $this->sut->getPeers());
        self::assertContains($this->peer, $this->sut->getPeers());
    }

    public function testFromArray()
    {
        $sut = new TransactionOptions([
            'peers' => [$this->peer],
        ]);

        self::assertTrue($sut->hasPeers());
        self::assertCount(1, $sut->getPeers());
        self::assertContains($this->peer, $sut->getPeers());
    }

    public function testFromMultiDimensionalArray()
    {
        $sut = new TransactionOptions([
            'peers' => [$this->peer],
        ]);

        self::assertCount(1, $sut->getPeers());
        self::assertContains($this->peer, $sut->getPeers());
    }

    public function testSetPeers()
    {
        $this->sut->setPeers([$this->peer]);

        self::assertCount(1, $this->sut->getPeers());
        self::assertContains($this->peer, $this->sut->getPeers());
    }

    public function testAddManyPeers()
    {
        $peer = $this->getMockBuilder(PeerInterface::class)
            ->getMock();

        $this->sut->addPeers($this->peer, $peer);

        self::assertCount(2, $this->sut->getPeers());
        self::assertContains($this->peer, $this->sut->getPeers());
        self::assertContains($peer, $this->sut->getPeers());
    }

    public function testAddPeersImmutable()
    {
        $this->sut->addPeers($this->peer);

        $peer = $this->getMockBuilder(PeerInterface::class)
            ->getMock();

        $result = $this->sut->withPeers($peer);

        self::assertCount(1, $result->getPeers());
        self::assertNotContains($this->peer, $result->getPeers());
        self::assertContains($peer, $result->getPeers());

        self::assertCount(1, $this->sut->getPeers());
        self::assertContains($this->peer, $this->sut->getPeers());
        self::assertNotContains($peer, $this->sut->getPeers());
    }
}
