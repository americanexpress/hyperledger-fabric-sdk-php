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

namespace AmericanExpressTest\HyperledgerFabricClient\Channel;

use AmericanExpress\HyperledgerFabricClient\Chaincode\Chaincode;
use AmericanExpress\HyperledgerFabricClient\Channel\Channel;
use AmericanExpress\HyperledgerFabricClient\Channel\ChannelProposalProcessorInterface;
use AmericanExpress\HyperledgerFabricClient\Proposal\ResponseCollection;
use AmericanExpress\HyperledgerFabricClient\ProtoFactory\ChaincodeHeaderExtensionFactory;
use AmericanExpress\HyperledgerFabricClient\ProtoFactory\ChaincodeProposalPayloadFactory;
use AmericanExpress\HyperledgerFabricClient\Transaction\TransactionOptions;
use Hyperledger\Fabric\Protos\Peer\ChaincodeID;
use PHPUnit\Framework\TestCase;

/**
 * @covers \AmericanExpress\HyperledgerFabricClient\Channel\Channel
 */
class ChannelTest extends TestCase
{
    /**
     * @var ChannelProposalProcessorInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $client;

    /**
     * @var Channel
     */
    private $sut;

    protected function setUp()
    {
        $this->client = $this->getMockBuilder(ChannelProposalProcessorInterface::class)
            ->getMock();

        $this->sut = new Channel('foo', $this->client);
    }

    public function testQueryByChaincode()
    {
        $this->client->method('processChannelProposal')
            ->willReturn($proposalResponse = new ResponseCollection());

        $result = $this->sut->queryByChainCode(
            new TransactionOptions([
                'peers' => [
                    [
                        'name' => 'peer1',
                    ],
                ],
            ]),
            (new ChaincodeID())
                ->setPath('FizBuz')
                ->setName('FooBar')
                ->setVersion('v12.34'),
            [
                'foo' => 'bar',
            ]
        );

        self::assertSame($proposalResponse, $result);
    }

    public function testChannelCanCreateChaincode()
    {
        $chainCode = $this->sut->getChaincode('FizBuz');

        self::assertInstanceOf(Chaincode::class, $chainCode);
        self::assertSame($chainCode->getName(), 'FizBuz');
    }

    public function testChannelCanProcessChaincodeProposal()
    {
        $this->client->method('processChannelProposal')
            ->willReturn($proposalResponse = new ResponseCollection());

        $chaincodeHeaderExtension = ChaincodeHeaderExtensionFactory::fromChaincodeId(
            (new ChaincodeID())
                ->setPath('FizBuz')
                ->setName('FooBar')
                ->setVersion('v12.34')
        );
        $chaincodeProposalPayload = ChaincodeProposalPayloadFactory::fromChaincodeInvocationSpecArgs([
            'foo' => 'bar',
        ]);

        $result = $this->sut->processChaincodeProposal(
            $chaincodeProposalPayload,
            $chaincodeHeaderExtension,
            new TransactionOptions([
                'peers' => [
                    [
                        'name' => 'peer1',
                    ],
                ],
            ])
        );

        self::assertSame($proposalResponse, $result);
    }
}
