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

use AmericanExpress\HyperledgerFabricClient\Peer\Peer;
use Grpc\UnaryCall;
use Hyperledger\Fabric\Protos\Peer\EndorserClient;
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

        $this->unaryCall = $this->getMockBuilder(UnaryCall::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->sut = new Peer($this->endorserClient);
    }
    public function testProcessChannelProposal()
    {
        $this->endorserClient->method('ProcessProposal')
            ->willReturn($this->unaryCall);

        $result = $this->sut->processSignedProposal(new SignedProposal());

        self::assertSame($this->unaryCall, $result);
    }
}
