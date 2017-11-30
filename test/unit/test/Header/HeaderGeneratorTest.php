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

namespace AmericanExpressTest\HyperledgerFabricClient\Header;

use AmericanExpress\HyperledgerFabricClient\Header\HeaderGenerator;
use AmericanExpress\HyperledgerFabricClient\Transaction\TransactionIdentifier;
use AmericanExpress\HyperledgerFabricClient\Transaction\TransactionIdentifierGeneratorInterface;
use Hyperledger\Fabric\Protos\Common\ChannelHeader;
use Hyperledger\Fabric\Protos\Common\Header;
use Hyperledger\Fabric\Protos\MSP\SerializedIdentity;
use PHPUnit\Framework\TestCase;

/**
 * @covers \AmericanExpress\HyperledgerFabricClient\Header\HeaderGenerator
 */
class HeaderGeneratorTest extends TestCase
{
    /**
     * @var TransactionIdentifierGeneratorInterface|\PHPUnit_Framework_MockObject_MockObject $headerGenerator
     */
    private $idGenerator;

    /**
     * @var HeaderGenerator $sut
     */
    private $sut;

    protected function setUp()
    {

        $this->idGenerator = $this->getMockBuilder(TransactionIdentifierGeneratorInterface::class)
            ->getMock();
        $this->sut = new HeaderGenerator($this->idGenerator, 0);
    }

    public function testGeneratorReturnsInstanceOfHeader()
    {
        $result = $this->sut->fromChannelHeader(new SerializedIdentity(), new ChannelHeader());
        self::assertInstanceOf(Header::class, $result);
    }

    public function testGeneratorPassesSerializedIdentifierToTransactionIdentifierGenerator()
    {
        $identity = new SerializedIdentity();
        $this->idGenerator->expects(self::once())
            ->method('fromSerializedIdentity')
            ->with($identity);

        $this->sut->fromChannelHeader($identity, new ChannelHeader());
    }

    public function testGeneratorAnnotatesChannelHeaderWithTransactionIdAndEpoch()
    {
        $identity = new SerializedIdentity();
        $this->idGenerator->expects(self::once())
            ->method('fromSerializedIdentity')
            ->willReturn($txId = new TransactionIdentifier('12345', '54321'));

        $channelHeader = new ChannelHeader();

        $this->sut->fromChannelHeader($identity, $channelHeader);

        self::assertEquals('12345', $channelHeader->getTxId());
        self::assertEquals(0, $channelHeader->getEpoch());
    }

    public function testGeneratorPassesConfiguredEpoch()
    {
        $this->sut = new HeaderGenerator($this->idGenerator, 42);

        $channelHeader = new ChannelHeader();

        $this->sut->fromChannelHeader(new SerializedIdentity(), $channelHeader);

        self::assertEquals(42, $channelHeader->getEpoch());
    }

    public function testGeneratorPassesConfiguredDefaultEpoch()
    {
        $this->sut = new HeaderGenerator($this->idGenerator);

        $channelHeader = new ChannelHeader();

        $this->sut->fromChannelHeader(new SerializedIdentity(), $channelHeader);

        self::assertEquals(0, $channelHeader->getEpoch());
    }
}
