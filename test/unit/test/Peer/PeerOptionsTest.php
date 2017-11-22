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

namespace AmericanExpressTest\HyperledgerFabricClient\Peer;

use AmericanExpress\HyperledgerFabricClient\Peer\PeerOptions;
use PHPUnit\Framework\TestCase;

/**
 * @covers \AmericanExpress\HyperledgerFabricClient\Peer\PeerOptions
 */
class PeerOptionsTest extends TestCase
{
    /**
     * @var PeerOptions
     */
    private $sut;

    protected function setUp()
    {
        $this->sut = new PeerOptions();
    }

    public function testName()
    {
        self::assertNull($this->sut->getName());

        $this->sut->setName('peer1');

        self::assertSame('peer1', $this->sut->getName());
    }

    public function testRequests()
    {
        self::assertNull($this->sut->getRequests());

        $this->sut->setRequests('localhost:7051');

        self::assertSame('localhost:7051', $this->sut->getRequests());
    }

    public function testEvents()
    {
        self::assertNull($this->sut->getEvents());

        $this->sut->setEvents('localhost:7053');

        self::assertSame('localhost:7053', $this->sut->getEvents());
    }

    public function testServerHostname()
    {
        self::assertNull($this->sut->getServerHostname());

        $this->sut->setServerHostname('peer0.org1.example.com');

        self::assertSame('peer0.org1.example.com', $this->sut->getServerHostname());
    }

    public function testTlsCaCerts()
    {
        self::assertNull($this->sut->getTlsCaCerts());

        $this->sut->setTlsCaCerts(__FILE__);

        self::assertSame(__FILE__, $this->sut->getTlsCaCerts());
    }

    public function testFromArray()
    {
        $sut = new PeerOptions([
            'name' => 'peer1',
            'requests' => 'localhost:7051',
            'events' => 'localhost:7053',
            'server-hostname' => 'peer0.org1.example.com',
            'tls_cacerts' => __FILE__,
        ]);

        self::assertSame('peer1', $sut->getName());
        self::assertSame('localhost:7051', $sut->getRequests());
        self::assertSame('localhost:7053', $sut->getEvents());
        self::assertSame('peer0.org1.example.com', $sut->getServerHostname());
        self::assertSame(__FILE__, $sut->getTlsCaCerts());
    }
}
