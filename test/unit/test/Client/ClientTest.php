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

namespace AmericanExpressTest\HyperledgerFabricClient\Client;

use AmericanExpress\HyperledgerFabricClient\Channel\ChannelManager;
use AmericanExpress\HyperledgerFabricClient\ChannelInterface;
use AmericanExpress\HyperledgerFabricClient\Client\Client;
use AmericanExpress\HyperledgerFabricClient\Config\ClientConfig;
use AmericanExpress\HyperledgerFabricClient\EndorserClientManagerInterface;
use AmericanExpress\HyperledgerFabricClient\Organization\OrganizationOptions;
use AmericanExpress\HyperledgerFabricClient\Signatory\SignatoryInterface;
use AmericanExpress\HyperledgerFabricClient\Transaction\TransactionRequest;
use Grpc\UnaryCall;
use Hyperledger\Fabric\Protos\MSP\SerializedIdentity;
use Hyperledger\Fabric\Protos\Peer\EndorserClient;
use Hyperledger\Fabric\Protos\Peer\Proposal;
use Hyperledger\Fabric\Protos\Peer\ProposalResponse;
use Hyperledger\Fabric\Protos\Peer\SignedProposal;
use PHPUnit\Framework\TestCase;

/**
 * @covers \AmericanExpress\HyperledgerFabricClient\Client\Client
 */
class ClientTest extends TestCase
{
    /**
     * @var UnaryCall|\PHPUnit_Framework_MockObject_MockObject
     */
    private $unaryCall;

    /**
     * @var SignatoryInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $signatory;

    /**
     * @var EndorserClient|\PHPUnit_Framework_MockObject_MockObject
     */
    private $endorserClient;

    /**
     * @var Client
     */
    private $sut;

    protected function setUp()
    {
        $this->endorserClient = $this->getMockBuilder(EndorserClient::class)
            ->disableOriginalConstructor()
            ->getMock();

        /** @var EndorserClientManagerInterface|\PHPUnit_Framework_MockObject_MockObject $endorserClients */
        $endorserClients = $this->getMockBuilder(EndorserClientManagerInterface::class)
            ->getMock();

        $endorserClients->method('get')
            ->willReturn($this->endorserClient);

        $this->signatory = $this->getMockBuilder(SignatoryInterface::class)
            ->getMock();

        $this->unaryCall = $this->getMockBuilder(UnaryCall::class)
            ->disableOriginalConstructor()
            ->getMock();

        $identity = new SerializedIdentity();

        $config = new ClientConfig([]);

        $this->sut = new Client($identity, $this->signatory, $endorserClients, $config);
    }

    public function testGetChannel()
    {
        $result = $this->sut->getChannel('foo');

        self::assertInstanceOf(ChannelInterface::class, $result);

        self::assertSame($result, $this->sut->getChannel('foo'));
    }

    public function testProcessProposal()
    {
        $proposal = new Proposal();

        $context = new TransactionRequest([
            'organization' => new OrganizationOptions([
                'peers' => [
                    [
                        'name' => 'peer1',
                        'requests' => 'localhost:7051',
                        'events' => 'localhost:7053',
                        'server-hostname' => 'peer0.org1.example.com',
                        'tls_cacerts' => __FILE__,
                    ],
                ],
                'privateKey' => __FILE__,
            ]),
            'peer' => 'peer1',
        ]);

        $this->endorserClient->method('ProcessProposal')
            ->willReturn($this->unaryCall);

        $this->unaryCall->method('wait')
            ->willReturn([
                $proposalResponse = new ProposalResponse(),
                [
                    'code' => 0,
                ]
            ]);

        $response = $this->sut->processProposal($proposal, $context);

        self::assertInstanceOf(ProposalResponse::class, $response);
    }

    /**
     * @expectedException \AmericanExpress\HyperledgerFabricClient\Exception\UnexpectedValueException
     */
    public function testProcessProposalRequiresUnaryCall()
    {
        $proposal = new Proposal();

        $context = new TransactionRequest([
            'organization' => new OrganizationOptions([
                'peers' => [
                    [
                        'name' => 'peer1',
                        'requests' => 'localhost:7051',
                        'events' => 'localhost:7053',
                        'server-hostname' => 'peer0.org1.example.com',
                        'tls_cacerts' => __FILE__,
                    ],
                ],
                'privateKey' => __FILE__,
            ]),
            'peer' => 'peer1',
        ]);

        $this->endorserClient->method('ProcessProposal')
            ->willReturn(new \stdClass());

        $this->sut->processProposal($proposal, $context);
    }

    /**
     * @expectedException \AmericanExpress\HyperledgerFabricClient\Exception\RuntimeException
     * @expectedExceptionMessage Connect failed
     * @expectedExceptionCode 14
     */
    public function testProcessSignedProposalHandlesConnectionError()
    {
        $proposal = new Proposal();

        $context = new TransactionRequest([
            'organization' => new OrganizationOptions([
                'peers' => [
                    [
                        'name' => 'peer1',
                        'requests' => 'localhost:7051',
                        'events' => 'localhost:7053',
                        'server-hostname' => 'peer0.org1.example.com',
                        'tls_cacerts' => __FILE__,
                    ],
                ],
                'privateKey' => __FILE__,
            ]),
            'peer' => 'peer1',
        ]);

        $this->endorserClient->method('ProcessProposal')
            ->willReturn($this->unaryCall);

        $this->unaryCall->method('wait')
            ->willReturn([
                null,
                [
                    'code' => 14,
                    'details' => 'Connect failed',
                    'metadata' => [],
                ]
            ]);

        $this->sut->processProposal($proposal, $context);
    }

    public function testGetIdentity()
    {
        self::assertInstanceOf(SerializedIdentity::class, $this->sut->getIdentity());
    }
}
