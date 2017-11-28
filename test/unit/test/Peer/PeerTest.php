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
use AmericanExpress\HyperledgerFabricClient\Peer\PeerOptions;
use Grpc\UnaryCall;
use Hyperledger\Fabric\Protos\Peer\EndorserClient;
use Hyperledger\Fabric\Protos\Peer\ProposalResponse;
use Hyperledger\Fabric\Protos\Peer\SignedProposal;
use PHPUnit\Framework\TestCase;

/**
 * @covers \AmericanExpress\HyperledgerFabricClient\Peer\Peer
 */
class PeerTest extends TestCase
{
    /**
     * @var UnaryCall|\PHPUnit_Framework_MockObject_MockObject
     */
    private $unaryCall;

    /**
     * @var EndorserClient|\PHPUnit_Framework_MockObject_MockObject
     */
    private $endorserClient;

    /**
     * @var Peer
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

        $this->unaryCall = $this->getMockBuilder(UnaryCall::class)
            ->disableOriginalConstructor()
            ->getMock();

        $options = new PeerOptions([
            'requests' => 'localhost:7051',
        ]);

        $this->sut = new Peer($options, $endorserClients);
    }

    public function testProcessChannelProposal()
    {
        $this->endorserClient->method('ProcessProposal')
            ->willReturn($this->unaryCall);

        $this->unaryCall->method('wait')
            ->willReturn([
                $proposalResponse = new ProposalResponse(),
                [ 'code' => 0 ]
            ]);

        $response = $this->sut->processSignedProposal(new SignedProposal());

        self::assertInstanceOf(ProposalResponse::class, $response);
    }

    public function testProcessChannelProposalWithCustomPeer()
    {
        $this->endorserClient->method('ProcessProposal')
            ->willReturn($this->unaryCall);

        $this->unaryCall->method('wait')
            ->willReturn([
                $proposalResponse = new ProposalResponse(),
                [
                    'code' => 0,
                ]
            ]);

        $response = $this->sut->processSignedProposal(new SignedProposal());

        self::assertInstanceOf(ProposalResponse::class, $response);
    }

    /**
     * @expectedException \AmericanExpress\HyperledgerFabricClient\Exception\UnexpectedValueException
     */
    public function testProcessProposalRequiresUnaryCall()
    {
        $this->endorserClient->method('ProcessProposal')
            ->willReturn(new \stdClass());

        $this->sut->processSignedProposal(new SignedProposal());
    }

    /**
     * @expectedException \AmericanExpress\HyperledgerFabricClient\Exception\RuntimeException
     * @expectedExceptionMessage Connect failed
     * @expectedExceptionCode 14
     */
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

        $this->sut->processSignedProposal(new SignedProposal());
    }
}
