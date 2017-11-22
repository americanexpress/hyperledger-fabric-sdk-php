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

use AmericanExpress\HyperledgerFabricClient\Channel;
use AmericanExpress\HyperledgerFabricClient\EndorserClientManagerInterface;
use AmericanExpress\HyperledgerFabricClient\Organization\OrganizationOptions;
use AmericanExpress\HyperledgerFabricClient\Signatory\SignatoryInterface;
use AmericanExpress\HyperledgerFabricClient\Transaction\TransactionContextFactoryInterface;
use AmericanExpress\HyperledgerFabricClient\Transaction\TransactionRequest;
use Grpc\UnaryCall;
use Hyperledger\Fabric\Protos\Peer\ChaincodeID;
use Hyperledger\Fabric\Protos\Peer\EndorserClient;
use Hyperledger\Fabric\Protos\Peer\ProposalResponse;
use PHPUnit\Framework\TestCase;

/**
 * @covers \AmericanExpress\HyperledgerFabricClient\Channel
 */
class ChannelTest extends TestCase
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
     * @var TransactionContextFactoryInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $transactionContextFactory;

    /**
     * @var Channel
     */
    private $sut;

    protected function setUp()
    {
        $this->endorserClient = self::getMockBuilder(EndorserClient::class)
            ->disableOriginalConstructor()
            ->getMock();

        /** @var EndorserClientManagerInterface|\PHPUnit_Framework_MockObject_MockObject $endorserClients */
        $endorserClients = self::getMockBuilder(EndorserClientManagerInterface::class)
            ->getMock();

        $endorserClients->method('get')
            ->willReturn($this->endorserClient);

        $this->transactionContextFactory = self::getMockBuilder(TransactionContextFactoryInterface::class)
            ->getMock();

        $this->signatory = self::getMockBuilder(SignatoryInterface::class)
            ->getMock();

        $this->unaryCall = self::getMockBuilder(UnaryCall::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->sut = new Channel('foo', $endorserClients, $this->transactionContextFactory, $this->signatory);
    }

    public function testQueryByChaincode()
    {
        $this->endorserClient->method('ProcessProposal')
            ->willReturn($this->unaryCall);

        $proposalResponse = new ProposalResponse();

        $this->unaryCall->method('wait')
            ->willReturn([
                $proposalResponse,
                [
                    'code' => 0,
                ]
            ]);

        $result = $this->sut->queryByChainCode(new TransactionRequest([
            'organization' => new OrganizationOptions([
                'mspId' => '1234',
                'adminCerts' => __FILE__,
                'privateKey' => __FILE__,
                'peers' => [
                    [
                        'name' => 'peer1',
                        'requests' => 'example.com',
                    ],
                ],
            ]),
            'peer' => 'peer1',
            'chaincodeId' => (new ChaincodeID())
                ->setPath('FizBuz')
                ->setName('FooBar')
                ->setVersion('v12.34'),
            'args' => [
                'foo' => 'bar',
            ],
        ]));

        self::assertSame($proposalResponse, $result);
    }

    /**
     * @expectedException \AmericanExpress\HyperledgerFabricClient\Exception\RuntimeException
     * @expectedExceptionMessage Connect failed
     * @expectedExceptionCode 14
     */
    public function testQueryByChaincodeConnectionFailure()
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

        $this->sut->queryByChainCode(new TransactionRequest([
            'organization' => new OrganizationOptions([
                'mspId' => '1234',
                'adminCerts' => __FILE__,
                'privateKey' => __FILE__,
                'peers' => [
                    [
                        'name' => 'peer1',
                        'requests' => 'example.com',
                    ],
                ],
            ]),
            'peer' => 'peer1',
            'chaincodeId' => (new ChaincodeID())
                ->setPath('FizBuz')
                ->setName('FooBar')
                ->setVersion('v12.34'),
            'args' => [
                'foo' => 'bar',
            ],
        ]));
    }
}
