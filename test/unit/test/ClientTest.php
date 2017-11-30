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

namespace AmericanExpressTest\HyperledgerFabricClient;

use AmericanExpress\HyperledgerFabricClient\Channel\ChannelInterface;
use AmericanExpress\HyperledgerFabricClient\Client;
use AmericanExpress\HyperledgerFabricClient\EndorserClient\EndorserClientManagerInterface;
use AmericanExpress\HyperledgerFabricClient\Exception\RuntimeException;
use AmericanExpress\HyperledgerFabricClient\Header\HeaderGeneratorInterface;
use AmericanExpress\HyperledgerFabricClient\Organization\OrganizationOptions;
use AmericanExpress\HyperledgerFabricClient\Peer\Peer;
use AmericanExpress\HyperledgerFabricClient\Peer\PeerFactory;
use AmericanExpress\HyperledgerFabricClient\Peer\PeerInterface;
use AmericanExpress\HyperledgerFabricClient\Proposal\ResponseCollection;
use AmericanExpress\HyperledgerFabricClient\Signatory\SignatoryInterface;
use AmericanExpress\HyperledgerFabricClient\Transaction\TransactionOptions;
use AmericanExpress\HyperledgerFabricClient\User\UserContext;
use Grpc\UnaryCall;
use Hyperledger\Fabric\Protos\MSP\SerializedIdentity;
use Hyperledger\Fabric\Protos\Peer\EndorserClient;
use Hyperledger\Fabric\Protos\Peer\Proposal;
use Hyperledger\Fabric\Protos\Peer\ProposalResponse;
use PHPUnit\Framework\TestCase;

/**
 * @covers \AmericanExpress\HyperledgerFabricClient\Client
 */
class ClientTest extends TestCase
{
    /**
     * @var PeerInterface
     */
    private $peer;

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
     * @var HeaderGeneratorInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $headerGenerator;

    /**
     * @var Client
     */
    private $sut;

    protected function setUp()
    {
        $this->endorserClient = $this->getMockBuilder(EndorserClient::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->peer = new Peer($this->endorserClient);

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

        $user = new UserContext($identity, new OrganizationOptions([
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
        ]));

        $this->headerGenerator = $this->getMockBuilder(HeaderGeneratorInterface::class)
            ->getMock();

        $this->sut = new Client(
            $user,
            $this->signatory,
            new PeerFactory($endorserClients),
            $this->headerGenerator
        );
    }

    public function testGetChannel()
    {
        $result = $this->sut->getChannel('foo');

        self::assertInstanceOf(ChannelInterface::class, $result);
        self::assertSame($result, $this->sut->getChannel('foo'));
    }

    public function testProcessProposal()
    {
        $this->endorserClient->method('ProcessProposal')
            ->willReturn($this->unaryCall);

        $this->unaryCall->method('wait')
            ->willReturn([
                new ProposalResponse(),
                [ 'code' => 0 ]
            ]);

        $context = new TransactionOptions([
            'peers' => [$this->peer],
        ]);

        $response = $this->sut->processProposal(new Proposal(), $context);

        self::assertInstanceOf(ResponseCollection::class, $response);
        self::assertCount(1, $response->getProposalResponses());
        self::assertCount(0, $response->getExceptions());
    }

    public function testProcessSignedProposalHandlesConnectionError()
    {
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

        $context = new TransactionOptions([
            'peers' => [$this->peer],
        ]);

        $response = $this->sut->processProposal(new Proposal(), $context);

        self::assertInstanceOf(ResponseCollection::class, $response);
        self::assertCount(0, $response->getProposalResponses());
        self::assertCount(1, $response->getExceptions());
    }

    /**
     * @expectedException RuntimeException
     */
    public function testThrowsRuntimeExceptionOnMissingPeers()
    {
        $this->sut->processProposal(new Proposal(), new TransactionOptions());
    }
}
