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

namespace AmericanExpressTest\HyperledgerFabricClient\Organization;

use AmericanExpress\HyperledgerFabricClient\Organization\OrganizationOptions;
use AmericanExpress\HyperledgerFabricClient\Peer\PeerOptionsInterface;
use PHPUnit\Framework\TestCase;

/**
 * @covers \AmericanExpress\HyperledgerFabricClient\Organization\OrganizationOptions
 */
class OrganizationOptionsTest extends TestCase
{
    /**
     * @var OrganizationOptions
     */
    private $sut;

    protected function setUp()
    {
        $this->sut = new OrganizationOptions();
    }

    public function testName()
    {
        self::assertNull($this->sut->getName());

        $this->sut->setName('FooBar');

        self::assertSame('FooBar', $this->sut->getName());
    }

    public function testMspId()
    {
        self::assertNull($this->sut->getMspId());

        $this->sut->setMspId('FooBar');

        self::assertSame('FooBar', $this->sut->getMspId());
    }

    public function testCa()
    {
        self::assertCount(0, $this->sut->getCa());

        $this->sut->setCa([
            'url' => 'https://localhost:7054',
            'name' => 'ca-org1'
        ]);

        self::assertSame([
            'url' => 'https://localhost:7054',
            'name' => 'ca-org1'
        ], $this->sut->getCa());
    }

    public function testAdminCerts()
    {
        self::assertNull($this->sut->getAdminCerts());

        $this->sut->setAdminCerts('FooBar');

        self::assertSame('FooBar', $this->sut->getAdminCerts());
    }

    public function testPrivateKey()
    {
        self::assertNull($this->sut->getPrivateKey());

        $this->sut->setPrivateKey('FooBar');

        self::assertSame('FooBar', $this->sut->getPrivateKey());
    }

    public function testPeers()
    {
        self::assertCount(0, $this->sut->getPeers());

        $this->sut->setPeers([
            [
                'name' => 'peer1',
                'requests' => 'localhost:7051',
                'events' => 'localhost:7053',
                'server-hostname' => 'peer1.org1.example.com',
                'tls_cacerts' => __FILE__,
            ],
            [
                'name' => 'peer2',
                'requests' => 'localhost:8051',
                'events' => 'localhost:8053',
                'server-hostname' => 'peer2.org1.example.com',
                'tls_cacerts' => __FILE__,
            ],
        ]);

        self::assertCount(2, $this->sut->getPeers());
        self::assertInstanceOf(PeerOptionsInterface::class, $this->sut->getPeers()[0]);
        self::assertInstanceOf(PeerOptionsInterface::class, $this->sut->getPeers()[1]);
    }

    public function testGetPeerByName()
    {
        $this->sut->setPeers([
            [
                'name' => 'peer1',
                'requests' => 'localhost:7051',
                'events' => 'localhost:7053',
                'server-hostname' => 'peer1.org1.example.com',
                'tls_cacerts' => __FILE__,
            ],
            [
                'name' => 'peer2',
                'requests' => 'localhost:8051',
                'events' => 'localhost:8053',
                'server-hostname' => 'peer2.org1.example.com',
                'tls_cacerts' => __FILE__,
            ],
        ]);

        $peer1 = $this->sut->getPeerByName('peer1');
        self::assertInstanceOf(PeerOptionsInterface::class, $peer1);
        self::assertSame('peer1', $peer1->getName());
        self::assertSame('localhost:7051', $peer1->getRequests());
        self::assertSame('localhost:7053', $peer1->getEvents());
        self::assertSame('peer1.org1.example.com', $peer1->getServerHostname());
        self::assertSame(__FILE__, $peer1->getTlsCaCerts());

        $peer2 = $this->sut->getPeerByName('peer2');
        self::assertInstanceOf(PeerOptionsInterface::class, $peer2);
        self::assertSame('peer2', $peer2->getName());
        self::assertSame('localhost:8051', $peer2->getRequests());
        self::assertSame('localhost:8053', $peer2->getEvents());
        self::assertSame('peer2.org1.example.com', $peer2->getServerHostname());
        self::assertSame(__FILE__, $peer2->getTlsCaCerts());
    }

    public function testGetPeerByInvalidName()
    {
        self::assertNull($this->sut->getPeerByName('peer1'));
    }

    public function testFromArray()
    {
        $sut = new OrganizationOptions([
            'name' => 'peerOrg1',
            'mspid' => 'Org1MSP',
            'ca' => [
                'url' => 'https://localhost:7054',
                'name' => 'ca-org1',
            ],
            'admin_certs' => __FILE__,
            'private_key' => __FILE__,
            'peers' => [
                [
                    'name' => 'peer1',
                    'requests' => 'localhost:7051',
                    'events' => 'localhost:7053',
                    'server-hostname' => 'peer1.org1.example.com',
                    'tls_cacerts' => __FILE__,
                ],
                [
                    'name' => 'peer2',
                    'requests' => 'localhost:8051',
                    'events' => 'localhost:8053',
                    'server-hostname' => 'peer2.org1.example.com',
                    'tls_cacerts' => __FILE__,
                ],
            ],
        ]);

        self::assertSame('peerOrg1', $sut->getName());
        self::assertSame('Org1MSP', $sut->getMspId());
        self::assertSame('https://localhost:7054', $sut->getCa()['url']);
        self::assertSame('ca-org1', $sut->getCa()['name']);
        self::assertSame(__FILE__, $sut->getAdminCerts());
        self::assertSame(__FILE__, $sut->getPrivateKey());
        self::assertInstanceOf(PeerOptionsInterface::class, $sut->getPeerByName('peer1'));
        self::assertInstanceOf(PeerOptionsInterface::class, $sut->getPeerByName('peer2'));
        self::assertNull($sut->getPeerByName('peer3'));
    }
}
