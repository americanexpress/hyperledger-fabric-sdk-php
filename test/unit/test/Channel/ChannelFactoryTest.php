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

use AmericanExpress\HyperledgerFabricClient\Channel\Channel;
use AmericanExpress\HyperledgerFabricClient\Channel\ChannelFactory;
use AmericanExpress\HyperledgerFabricClient\Header\HeaderGeneratorInterface;
use AmericanExpress\HyperledgerFabricClient\Organization\OrganizationOptions;
use AmericanExpress\HyperledgerFabricClient\Peer\PeerFactoryInterface;
use AmericanExpress\HyperledgerFabricClient\Proposal\ProposalProcessorInterface;
use AmericanExpress\HyperledgerFabricClient\User\UserContext;
use Hyperledger\Fabric\Protos\MSP\SerializedIdentity;
use PHPUnit\Framework\TestCase;

/**
 * @covers \AmericanExpress\HyperledgerFabricClient\Channel\ChannelFactory
 */
class ChannelFactoryTest extends TestCase
{
    /**
     * @var ProposalProcessorInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $proposalProcessor;

    /**
     * @var PeerFactoryInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $peerFactory;

    /**
     * @var HeaderGeneratorInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $headerGenerator;

    /**
     * @var ChannelFactory
     */
    private $sut;

    protected function setUp()
    {
        $this->proposalProcessor = $this->getMockBuilder(ProposalProcessorInterface::class)
            ->getMock();

        $this->headerGenerator = $this->getMockBuilder(HeaderGeneratorInterface::class)
            ->getMock();

        $this->peerFactory = $this->getMockBuilder(PeerFactoryInterface::class)
            ->getMock();

        $this->sut = new ChannelFactory($this->headerGenerator, $this->peerFactory);
    }

    public function testCreateWithPeers()
    {
        $user = new UserContext(
            new SerializedIdentity(),
            new OrganizationOptions([
                'peers' => [
                    [
                        'requests' => 'localhost:8000',
                    ],
                    [
                        'requests' => 'localhost:9000',
                    ],
                ],
            ])
        );

        $result = $this->sut->create('FooBar', $this->proposalProcessor, $user);

        self::assertInstanceOf(Channel::class, $result);
        self::assertCount(2, $result->getPeers());
    }

    public function testCreateWithoutPeers()
    {
        $user = new UserContext(
            new SerializedIdentity(),
            new OrganizationOptions()
        );

        $result = $this->sut->create('FooBar', $this->proposalProcessor, $user);

        self::assertInstanceOf(Channel::class, $result);
        self::assertCount(0, $result->getPeers());
    }
}
