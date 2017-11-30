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
use AmericanExpress\HyperledgerFabricClient\Exception\RuntimeException;
use AmericanExpress\HyperledgerFabricClient\Peer\PeerOptions;
use AmericanExpress\HyperledgerFabricClient\Proposal\ResponseCollection;
use AmericanExpress\HyperledgerFabricClient\Proposal\ProposalProcessorInterface;
use AmericanExpress\HyperledgerFabricClient\ProtoFactory\ChaincodeHeaderExtensionFactory;
use AmericanExpress\HyperledgerFabricClient\ProtoFactory\ChaincodeProposalPayloadFactory;
use AmericanExpress\HyperledgerFabricClient\Transaction\TransactionOptions;
use AmericanExpress\HyperledgerFabricClient\Identity\SerializedIdentityAwareHeaderGeneratorInterface;
use AmericanExpressTest\HyperledgerFabricClient\TestAsset\MockProposalProcessor;
use Hyperledger\Fabric\Protos\Peer\ChaincodeID;
use PHPUnit\Framework\TestCase;

/**
 * @covers \AmericanExpress\HyperledgerFabricClient\Channel\Channel
 */
class ChannelTest extends TestCase
{
    /**
     * @var ProposalProcessorInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $client;

    /**
     * @var SerializedIdentityAwareHeaderGeneratorInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $headerGenerator;

    /**
     * @var Channel
     */
    private $sut;

    protected function setUp()
    {
        $this->client = $this->getMockBuilder(ProposalProcessorInterface::class)
            ->getMock();

        $this->headerGenerator = $this->getMockBuilder(SerializedIdentityAwareHeaderGeneratorInterface::class)
            ->getMock();

        $this->sut = new Channel('foo', $this->client, $this->headerGenerator);
    }

    public function testPeersIsEmptyCollectionByDefault()
    {
        self::assertCount(0, $this->sut->getPeers());
    }

    public function testCanSpecifyInitialPeers()
    {
        $peerOptions = new PeerOptions();
        $peerOptions->setName('peer1');
        $this->sut = new Channel(
            'foo',
            $this->client,
            $this->headerGenerator,
            [ $peerOptions ]
        );
        self::assertCount(1, $this->sut->getPeers());
        self::assertContains($peerOptions, $this->sut->getPeers());
    }

    public function testCanAddPeers()
    {
        $peerOptions = new PeerOptions();
        $peerOptions->setName('peer1');

        $this->sut->addPeer($peerOptions);

        self::assertCount(1, $this->sut->getPeers());
        self::assertContains($peerOptions, $this->sut->getPeers());
    }

    public function testQueryByChaincode()
    {
        $this->client->method('processProposal')
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
        $this->client->method('processProposal')
            ->willReturn($proposalResponse = new ResponseCollection());

        $result = $this->doChaincodeProposal(new TransactionOptions([
            'peers' => [
                [
                    'name' => 'peer1',
                ],
            ],
        ]));
        self::assertSame($proposalResponse, $result);
    }

    public function testChannelCanProcessChaincodeProposalWithDefaultPeers()
    {
        $peerOptions = new PeerOptions();
        $peerOptions->setName('peerInitial');

        $this->sut = new Channel(
            'foo',
            $processor = new MockProposalProcessor(),
            $this->headerGenerator,
            [ $peerOptions ]
        );

        $this->doChaincodeProposal(null, $this->sut);
        self::assertSame('peerInitial', $processor->getTransactionOptions()->getPeers()[0]->getName());
    }

    public function testChannelCanProcessChaincodeProposalAndOverrideDefaultPeers()
    {
        $peerInitial = new PeerOptions();
        $peerInitial->setName('peerInitial');

        $peerOverride = new PeerOptions();
        $peerOverride->setName('peerOverride');

        $this->sut = new Channel(
            'foo',
            $processor = new MockProposalProcessor(),
            $this->headerGenerator,
            [ $peerInitial ]
        );

        $transactionOptions = new TransactionOptions();
        $transactionOptions->addPeers($peerOverride);

        $this->doChaincodeProposal($transactionOptions);
        self::assertSame('peerOverride', $processor->getTransactionOptions()->getPeers()[0]->getName());
    }

    /**
     * @expectedException RuntimeException
     */
    public function testThrowsRuntimeExceptionOnMissingPeers()
    {
        $this->client->method('processProposal')
            ->willReturn($proposalResponse = new ResponseCollection());

        $this->doChaincodeProposal();
    }

    public function createChaincodeHeaderExtension()
    {
        return ChaincodeHeaderExtensionFactory::fromChaincodeId(
            (new ChaincodeID())
                ->setPath('FizBuz')
                ->setName('FooBar')
                ->setVersion('v12.34')
        );
    }

    public function createChaincodeProposalPayload()
    {
        return ChaincodeProposalPayloadFactory::fromChaincodeInvocationSpecArgs([
            'foo' => 'bar',
        ]);
    }

    public function doChaincodeProposal(TransactionOptions $options = null, Channel $channel = null)
    {
        if ($channel === null) {
            $channel = $this->sut;
        }

        return $channel->processChaincodeProposal(
            $this->createChaincodeProposalPayload(),
            $this->createChaincodeHeaderExtension(),
            $options
        );
    }
}
