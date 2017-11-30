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

use AmericanExpress\HyperledgerFabricClient\EndorserClient\EndorserClientManagerInterface;
use AmericanExpress\HyperledgerFabricClient\Peer\Peer;
use AmericanExpress\HyperledgerFabricClient\Peer\PeerFactory;
use AmericanExpress\HyperledgerFabricClient\Peer\PeerOptions;
use Hyperledger\Fabric\Protos\Peer\EndorserClient;
use PHPUnit\Framework\TestCase;

/**
 * @covers \AmericanExpress\HyperledgerFabricClient\Peer\PeerFactory
 */
class PeerFactoryTest extends TestCase
{
    /**
     * @var EndorserClient|\PHPUnit_Framework_MockObject_MockObject
     */
    private $endorserClient;

    /**
     * @var EndorserClientManagerInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $endorserClients;

    /**
     * @var PeerFactory
     */
    private $sut;

    protected function setUp()
    {
        $this->endorserClients = $this->getMockBuilder(EndorserClientManagerInterface::class)
            ->getMock();

        $this->endorserClient = $this->getMockBuilder(EndorserClient::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->sut = new PeerFactory($this->endorserClients);
    }

    public function testFromPeerOptions()
    {
        $this->endorserClients->method('get')
            ->with('localhost:8080')
            ->willReturn($this->endorserClient);

        $result = $this->sut->fromPeerOptions(new PeerOptions([
            'requests' => 'localhost:8080',
        ]));

        self::assertInstanceOf(Peer::class, $result);
    }

    public function testFromArray()
    {
        $this->endorserClients->method('get')
            ->with('localhost:8080')
            ->willReturn($this->endorserClient);

        $result = $this->sut->fromArray([
            'requests' => 'localhost:8080',
        ]);

        self::assertInstanceOf(Peer::class, $result);
    }
}
