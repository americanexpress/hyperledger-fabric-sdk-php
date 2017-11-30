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

use AmericanExpress\HyperledgerFabricClient\Peer\UnaryCallResolver;
use AmericanExpress\HyperledgerFabricClient\Proposal\Response;
use AmericanExpress\HyperledgerFabricClient\Proposal\ResponseCollection;
use Grpc\UnaryCall;
use Hyperledger\Fabric\Protos\Peer\ProposalResponse;
use PHPUnit\Framework\TestCase;

/**
 * @covers \AmericanExpress\HyperledgerFabricClient\Peer\UnaryCallResolver
 */
class UnaryCallResolverTest extends TestCase
{
    /**
     * @var UnaryCall|\PHPUnit_Framework_MockObject_MockObject
     */
    private $unaryCall;

    /**
     * @var UnaryCallResolver
     */
    private $sut;

    protected function setUp()
    {
        $this->unaryCall = $this->getMockBuilder(UnaryCall::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->sut = new UnaryCallResolver();
    }

    public function testResolveOneProposalResponse()
    {
        $this->unaryCall->method('wait')
            ->willReturn([
                $proposalResponse = new ProposalResponse(),
                [ 'code' => 0 ]
            ]);

        $response = $this->sut->resolveOne($this->unaryCall);

        self::assertInstanceOf(Response::class, $response);
        self::assertFalse($response->isException());
    }

    public function testResolveOneException()
    {
        $this->unaryCall->method('wait')
            ->willReturn([
                null,
                [
                    'code' => 14,
                    'details' => 'Connect failed',
                    'metadata' => [],
                ],
            ]);

        $response = $this->sut->resolveOne($this->unaryCall);

        self::assertInstanceOf(Response::class, $response);
        self::assertTrue($response->isException());
    }

    public function testResolveManyProposalResponses()
    {
        $this->unaryCall->method('wait')
            ->willReturn([
                new ProposalResponse(),
                [ 'code' => 0 ]
            ]);

        $responses = $this->sut->resolveMany($this->unaryCall, $this->unaryCall);

        self::assertInstanceOf(ResponseCollection::class, $responses);
        self::assertTrue($responses->hasProposalResponses());
        self::assertCount(2, $responses->getProposalResponses());
        self::assertFalse($responses->hasExceptions());
        self::assertCount(0, $responses->getExceptions());
    }

    public function testResolveManyExceptions()
    {
        $this->unaryCall->method('wait')
            ->willReturn([
                null,
                [
                    'code' => 14,
                    'details' => 'Connect failed',
                    'metadata' => [],
                ],
            ]);

        $responses = $this->sut->resolveMany($this->unaryCall, $this->unaryCall);

        self::assertInstanceOf(ResponseCollection::class, $responses);
        self::assertFalse($responses->hasProposalResponses());
        self::assertCount(0, $responses->getProposalResponses());
        self::assertTrue($responses->hasExceptions());
        self::assertCount(2, $responses->getExceptions());
    }

    public function testResolveManyMixedResponses()
    {
        $this->unaryCall->method('wait')
            ->willReturnOnConsecutiveCalls(
                [
                    new ProposalResponse(),
                    [ 'code' => 0 ]
                ],
                [
                    null,
                    [
                        'code' => 14,
                        'details' => 'Connect failed',
                        'metadata' => [],
                    ],
                ]
            );

        $responses = $this->sut->resolveMany($this->unaryCall, $this->unaryCall);

        self::assertInstanceOf(ResponseCollection::class, $responses);
        self::assertTrue($responses->hasProposalResponses());
        self::assertCount(1, $responses->getProposalResponses());
        self::assertTrue($responses->hasExceptions());
        self::assertCount(1, $responses->getExceptions());
    }
}
