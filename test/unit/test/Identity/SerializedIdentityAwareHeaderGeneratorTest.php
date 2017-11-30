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

namespace AmericanExpressTest\HyperledgerFabricClient\Identity;

use AmericanExpress\HyperledgerFabricClient\Header\HeaderGeneratorInterface;
use AmericanExpress\HyperledgerFabricClient\Identity\SerializedIdentityAwareHeaderGenerator;
use Hyperledger\Fabric\Protos\Common\ChannelHeader;
use Hyperledger\Fabric\Protos\Common\Header;
use Hyperledger\Fabric\Protos\MSP\SerializedIdentity;
use PHPUnit\Framework\TestCase;

/**
 * @covers \AmericanExpress\HyperledgerFabricClient\Identity\SerializedIdentityAwareHeaderGenerator
 */
class SerializedIdentityAwareHeaderGeneratorTest extends TestCase
{
    /**
     * @var SerializedIdentity $identity
     */
    private $identity;

    /**
     * @var HeaderGeneratorInterface|\PHPUnit_Framework_MockObject_MockObject $headerGenerator
     */
    private $headerGenerator;

    /**
     * @var SerializedIdentityAwareHeaderGenerator $sut
     */
    private $sut;

    protected function setUp()
    {
        $this->identity = new SerializedIdentity();
        $this->headerGenerator = $this->getMockBuilder(HeaderGeneratorInterface::class)
            ->getMock();
        $this->sut = new SerializedIdentityAwareHeaderGenerator($this->identity, $this->headerGenerator);
    }

    public function testGeneratorProxyPassesIdentityToHeaderGenerator()
    {
        $channelHeader = new ChannelHeader();

        $this->headerGenerator->expects(self::once())
            ->method('fromChannelHeader')
            ->with($this->identity, $channelHeader);

        $this->sut->generateHeader($channelHeader);
    }

    public function testGeneratorProxyReturnsHeaderFromGenerator()
    {
        $channelHeader = new ChannelHeader();

        $this->headerGenerator->method('fromChannelHeader')
            ->willReturn($header = new Header());

        $result = $this->sut->generateHeader($channelHeader);
        self::assertSame($header, $result);
    }
}
