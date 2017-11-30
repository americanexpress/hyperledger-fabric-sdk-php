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

namespace AmericanExpress\HyperledgerFabricClient\Channel;

use AmericanExpress\HyperledgerFabricClient\Header\HeaderGeneratorInterface;
use AmericanExpress\HyperledgerFabricClient\Identity\SerializedIdentityAwareHeaderGenerator;
use AmericanExpress\HyperledgerFabricClient\Identity\SerializedIdentityAwareHeaderGeneratorInterface;
use AmericanExpress\HyperledgerFabricClient\Peer\PeerFactory;
use AmericanExpress\HyperledgerFabricClient\Peer\PeerFactoryInterface;
use AmericanExpress\HyperledgerFabricClient\Peer\PeerInterface;
use AmericanExpress\HyperledgerFabricClient\Peer\PeerOptionsInterface;
use AmericanExpress\HyperledgerFabricClient\Proposal\ProposalProcessorInterface;
use AmericanExpress\HyperledgerFabricClient\User\UserContextInterface;
use Hyperledger\Fabric\Protos\MSP\SerializedIdentity;

final class ChannelFactory implements ChannelFactoryInterface
{
    /**
     * @var HeaderGeneratorInterface
     */
    private $headerGenerator;

    /**
     * @var PeerFactoryInterface
     */
    private $peerFactory;

    /**
     * ChannelFactory constructor.
     * @param HeaderGeneratorInterface $headerGenerator
     * @param PeerFactoryInterface|null $peerFactory
     */
    public function __construct(HeaderGeneratorInterface $headerGenerator, PeerFactoryInterface $peerFactory = null)
    {
        $this->headerGenerator = $headerGenerator;
        $this->peerFactory = $peerFactory ?: new PeerFactory();
    }

    /**
     * @param string $name
     * @param ProposalProcessorInterface $proposalProcessor
     * @param UserContextInterface $user
     * @return ChannelInterface
     */
    public function create(
        string $name,
        ProposalProcessorInterface $proposalProcessor,
        UserContextInterface $user
    ): ChannelInterface {
        $headerGenerator = $this->createSerializedIdentityAwareHeaderGenerator($user->getIdentity());

        $peers = $this->getPeers($user);

        return new Channel($name, $proposalProcessor, $headerGenerator, $peers);
    }

    /**
     * @param UserContextInterface $user
     * @return PeerInterface[]
     */
    private function getPeers(UserContextInterface $user): array
    {
        return \array_map(function (PeerOptionsInterface $options) {
            return $this->peerFactory->fromPeerOptions($options);
        }, $user->getOrganization()->getPeers());
    }

    /**
     * @param SerializedIdentity $identity
     * @return SerializedIdentityAwareHeaderGeneratorInterface
     */
    private function createSerializedIdentityAwareHeaderGenerator(
        SerializedIdentity $identity
    ): SerializedIdentityAwareHeaderGeneratorInterface {
        return new SerializedIdentityAwareHeaderGenerator($identity, $this->headerGenerator);
    }
}
