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

use AmericanExpress\HyperledgerFabricClient\Chaincode;
use AmericanExpress\HyperledgerFabricClient\Channel;
use AmericanExpress\HyperledgerFabricClient\Client\ClientInterface;
use AmericanExpress\HyperledgerFabricClient\Peer\PeerOptions;
use AmericanExpress\HyperledgerFabricClient\ProtoFactory\ChaincodeHeaderExtensionFactory;
use AmericanExpress\HyperledgerFabricClient\ProtoFactory\ChaincodeProposalPayloadFactory;
use AmericanExpress\HyperledgerFabricClient\Transaction\TransactionRequest;
use Hyperledger\Fabric\Protos\Peer\ChaincodeID;
use Hyperledger\Fabric\Protos\Peer\ProposalResponse;
use PHPUnit\Framework\TestCase;

/**
 * @covers \AmericanExpress\HyperledgerFabricClient\Channel
 */
class ChannelTest extends TestCase
{
    /**
     * @var ClientInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $client;

    /**
     * @var Channel
     */
    private $sut;

    protected function setUp()
    {
        $this->client = $this->getMockBuilder(ClientInterface::class)
            ->getMock();

        $this->sut = new Channel('foo', $this->client);
    }

    public function testQueryByChaincode()
    {
        $this->client->method('processProposal')
            ->willReturn($proposalResponse = new ProposalResponse());

        $result = $this->sut->queryByChainCode(
            new TransactionRequest([
                'peer' => new PeerOptions([
                    'name' => 'peer1',
                ]),
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
        $this->client->method('processProposal')
            ->willReturn($proposalResponse = new ProposalResponse());

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
            new TransactionRequest([
                'peer' => new PeerOptions([
                    'name' => 'peer1',
                ]),
            ])
        );

        self::assertSame($proposalResponse, $result);
    }
}
