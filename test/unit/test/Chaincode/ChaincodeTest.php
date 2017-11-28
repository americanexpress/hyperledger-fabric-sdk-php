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

namespace AmericanExpressTest\HyperledgerFabricClient\Chaincode;

use AmericanExpress\HyperledgerFabricClient\Chaincode\Chaincode;
use AmericanExpress\HyperledgerFabricClient\Chaincode\ChaincodeProposalProcessorInterface;
use AmericanExpress\HyperledgerFabricClient\Peer\PeerOptions;
use AmericanExpress\HyperledgerFabricClient\ProtoFactory\ChaincodeHeaderExtensionFactory;
use AmericanExpress\HyperledgerFabricClient\ProtoFactory\ChaincodeProposalPayloadFactory;
use AmericanExpress\HyperledgerFabricClient\Transaction\TransactionOptions;
use Hyperledger\Fabric\Protos\Peer\ChaincodeID;
use Hyperledger\Fabric\Protos\Peer\ProposalResponse;
use PHPUnit\Framework\TestCase;

/**
 * @covers \AmericanExpress\HyperledgerFabricClient\Chaincode
 */
class ChaincodeTest extends TestCase
{
    /**
     * @var ChaincodeProposalProcessorInterface | \PHPUnit_Framework_MockObject_MockObject
     */
    private $channel;

    /**
     * @var Chaincode
     */
    private $sut;

    protected function setUp()
    {
        $this->channel = $this->getMockBuilder(ChaincodeProposalProcessorInterface::class)
            ->getMock();


        $this->sut = new Chaincode('foo', $this->channel);
    }

    public function testNameAccessorMethod()
    {
        self::assertSame('foo', $this->sut->getName());
    }

    public function testDefaultVersionAccessorMethod()
    {
        self::assertSame('', $this->sut->getVersion());
    }

    public function testDefaultPathAccessorMethod()
    {
        self::assertSame('', $this->sut->getPath());
    }

    public function testFullSpecification()
    {
        $this->sut = new Chaincode(['name' => 'foo', 'path' => 'bar', 'version' => '12.34'], $this->channel);
        self::assertSame('foo', $this->sut->getName());
        self::assertSame('bar', $this->sut->getPath());
        self::assertSame('12.34', $this->sut->getVersion());
    }

    /**
     * @expectedException \AmericanExpress\HyperledgerFabricClient\Exception\InvalidArgumentException
     */
    public function testNoNameSuppliedThrowsException()
    {
        $this->sut = new Chaincode(['path' => 'bar', 'version' => '12.34'], $this->channel);
    }

    /**
     * @expectedException \AmericanExpress\HyperledgerFabricClient\Exception\InvalidArgumentException
     */
    public function testEmptyNameSuppliedThrowsException()
    {
        $this->sut = new Chaincode(['name' => '', 'path' => 'bar', 'version' => '12.34'], $this->channel);
    }

    /**
     * @expectedException \AmericanExpress\HyperledgerFabricClient\Exception\InvalidArgumentException
     */
    public function testInvalidNameParameterSuppliedThrowsException()
    {
        $this->sut = new Chaincode(new \stdClass(), $this->channel);
    }

    public function testInvokeReturnsResponseFromChannel()
    {
        $this->channel->method('processChaincodeProposal')
            ->willReturn($proposalResponse = new ProposalResponse());

        $response = $this->sut->invoke('query', 'a');

        self::assertSame($proposalResponse, $response);
    }

    public function testAnyUnspecifiedMethodReturnsResponseFromChannel()
    {
        $this->channel->method('processChaincodeProposal')
            ->willReturn($proposalResponse = new ProposalResponse());

        $response = $this->sut->somethingElseEntirely('query', 'a');

        self::assertSame($proposalResponse, $response);
    }

    public function testCallSendsChainCodeProposalToChannel()
    {
        $chaincodeHeaderExtension = ChaincodeHeaderExtensionFactory::fromChaincodeId(
            (new ChaincodeID())->setName($this->sut->getName())
        );

        $chaincodeProposalPayload = ChaincodeProposalPayloadFactory::fromChaincodeInvocationSpecArgs(
            ['invoke', 'query', 'a']
        );

        $this->channel->expects(self::once())->method('processChaincodeProposal')
            ->with($chaincodeProposalPayload, $chaincodeHeaderExtension, null);

        $this->sut->__call('invoke', ['query', 'a']);
    }

    public function testCallSendsArgumentlessChainCodeProposalToChannel()
    {
        $chaincodeHeaderExtension = ChaincodeHeaderExtensionFactory::fromChaincodeId(
            (new ChaincodeID())->setName($this->sut->getName())
        );

        $chaincodeProposalPayload = ChaincodeProposalPayloadFactory::fromChaincodeInvocationSpecArgs(
            ['invoke']
        );

        $this->channel->expects(self::once())->method('processChaincodeProposal')
            ->with($chaincodeProposalPayload, $chaincodeHeaderExtension, null);

        $this->sut->__call('invoke', []);
    }

    public function testCallPassesTransactionRequestToChannel()
    {
        $transactionRequest = new TransactionOptions([
            'peer' => new PeerOptions([
                'name' => 'peer1',
            ]),
        ]);

        $chaincodeHeaderExtension = ChaincodeHeaderExtensionFactory::fromChaincodeId(
            (new ChaincodeID())->setName($this->sut->getName())
        );

        $chaincodeProposalPayload = ChaincodeProposalPayloadFactory::fromChaincodeInvocationSpecArgs(
            ['invoke']
        );

        $this->channel->expects(self::once())->method('processChaincodeProposal')
            ->with($chaincodeProposalPayload, $chaincodeHeaderExtension, $transactionRequest);

        $this->sut->__call('invoke', [$transactionRequest]);
    }

    public function testCallPassesTransactionRequestToChannelAsFinalArgument()
    {
        $transactionRequest = new TransactionOptions([
            'peer' => new PeerOptions([
                'name' => 'peer1',
            ]),
        ]);

        $chaincodeHeaderExtension = ChaincodeHeaderExtensionFactory::fromChaincodeId(
            (new ChaincodeID())->setName($this->sut->getName())
        );

        $chaincodeProposalPayload = ChaincodeProposalPayloadFactory::fromChaincodeInvocationSpecArgs(
            ['invoke', 'query', 'a']
        );

        $this->channel->expects(self::once())->method('processChaincodeProposal')
            ->with($chaincodeProposalPayload, $chaincodeHeaderExtension, $transactionRequest);

        $this->sut->__call('invoke', ['query', 'a', $transactionRequest]);
    }

    public function testInvokePassesTransactionRequestToChannelAsFinalArgument()
    {
        $transactionRequest = new TransactionOptions([
            'peer' => new PeerOptions([
                'name' => 'peer1',
            ]),
        ]);

        $chaincodeHeaderExtension = ChaincodeHeaderExtensionFactory::fromChaincodeId(
            (new ChaincodeID())->setName($this->sut->getName())
        );

        $chaincodeProposalPayload = ChaincodeProposalPayloadFactory::fromChaincodeInvocationSpecArgs(
            ['invoke', 'query', 'a']
        );

        $this->channel->expects(self::once())->method('processChaincodeProposal')
            ->with($chaincodeProposalPayload, $chaincodeHeaderExtension, $transactionRequest);

        $this->sut->invoke('query', 'a', $transactionRequest);
    }
}
