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
use AmericanExpress\HyperledgerFabricClient\Organization\OrganizationOptions;
use AmericanExpress\HyperledgerFabricClient\Proposal\ResponseCollection;
use AmericanExpress\HyperledgerFabricClient\ProtoFactory\ChannelHeaderFactory;
use AmericanExpress\HyperledgerFabricClient\Signatory\SignatoryInterface;
use AmericanExpress\HyperledgerFabricClient\Transaction\TransactionIdentifierGeneratorInterface;
use AmericanExpress\HyperledgerFabricClient\Transaction\TransactionOptions;
use AmericanExpress\HyperledgerFabricClient\User\UserContext;
use Grpc\UnaryCall;
use Hyperledger\Fabric\Protos\MSP\SerializedIdentity;
use Hyperledger\Fabric\Protos\Peer\EndorserClient;
use Hyperledger\Fabric\Protos\Peer\ProposalResponse;
use PHPUnit\Framework\TestCase;

/**
 * @covers \AmericanExpress\HyperledgerFabricClient\Client
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
     * @var TransactionIdentifierGeneratorInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $transactionIdGenerator;

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

        $this->transactionIdGenerator = $this->getMockBuilder(TransactionIdentifierGeneratorInterface::class)
            ->getMock();

        $this->sut = new Client($user, $this->signatory, $endorserClients, $this->transactionIdGenerator);
    }

    public function testGetChannel()
    {
        $result = $this->sut->getChannel('foo');

        self::assertInstanceOf(ChannelInterface::class, $result);

        self::assertSame($result, $this->sut->getChannel('foo'));
    }

    public function testProcessChannelProposal()
    {
        $channelHeader = ChannelHeaderFactory::create('test-channel');

        $this->endorserClient->method('ProcessProposal')
            ->willReturn($this->unaryCall);

        $this->unaryCall->method('wait')
            ->willReturn([
                $proposalResponse = new ProposalResponse(),
                [ 'code' => 0 ]
            ]);

        $response = $this->sut->processChannelProposal($channelHeader, 'test-payload');

        self::assertInstanceOf(ResponseCollection::class, $response);
        self::assertCount(1, $response->getProposalResponses());
        self::assertCount(0, $response->getExceptions());
    }

    public function testProcessChannelProposalWithCustomPeer()
    {
        $channelHeader = ChannelHeaderFactory::create('test-channel');

        $context = new TransactionOptions([
            'peers' => [
                [
                    'name' => 'peer1',
                    'requests' => 'localhost:7051',
                ],
            ],
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

        $response = $this->sut->processChannelProposal($channelHeader, 'test-payload', $context);

        self::assertInstanceOf(ResponseCollection::class, $response);
        self::assertCount(1, $response->getProposalResponses());
        self::assertCount(0, $response->getExceptions());
    }

    public function testProcessProposalRequiresUnaryCall()
    {
        $this->endorserClient->method('ProcessProposal')
            ->willReturn(new \stdClass());

        $channelHeader = ChannelHeaderFactory::create('test-channel');

        $response = $this->sut->processChannelProposal($channelHeader, 'test-payload');

        self::assertInstanceOf(ResponseCollection::class, $response);
        self::assertCount(0, $response->getProposalResponses());
        self::assertCount(1, $response->getExceptions());
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

        $channelHeader = ChannelHeaderFactory::create('test-channel');

        $response = $this->sut->processChannelProposal($channelHeader, 'test-payload');

        self::assertInstanceOf(ResponseCollection::class, $response);
        self::assertCount(0, $response->getProposalResponses());
        self::assertCount(1, $response->getExceptions());
    }
}
