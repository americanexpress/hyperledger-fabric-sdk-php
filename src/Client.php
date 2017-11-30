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

namespace AmericanExpress\HyperledgerFabricClient;

use AmericanExpress\HyperledgerFabricClient\Channel\ChannelFactoryInterface;
use AmericanExpress\HyperledgerFabricClient\Channel\ChannelInterface;
use AmericanExpress\HyperledgerFabricClient\Channel\ChannelProviderInterface;
use AmericanExpress\HyperledgerFabricClient\Exception\RuntimeException;
use AmericanExpress\HyperledgerFabricClient\Peer\PeerInterface;
use AmericanExpress\HyperledgerFabricClient\Peer\UnaryCallResolver;
use AmericanExpress\HyperledgerFabricClient\Proposal\ProposalProcessorInterface;
use AmericanExpress\HyperledgerFabricClient\Peer\UnaryCallResolverInterface;
use AmericanExpress\HyperledgerFabricClient\Proposal\ResponseCollection;
use AmericanExpress\HyperledgerFabricClient\Signatory\SignatoryInterface;
use AmericanExpress\HyperledgerFabricClient\Transaction\TransactionOptions;
use AmericanExpress\HyperledgerFabricClient\User\UserContextInterface;
use Hyperledger\Fabric\Protos\Peer\Proposal;
use Hyperledger\Fabric\Protos\Peer\SignedProposal;

final class Client implements ChannelProviderInterface, ProposalProcessorInterface
{
    /**
     * @var UserContextInterface
     */
    private $user;

    /**
     * @var ChannelFactoryInterface
     */
    private $channelFactory;

    /**
     * @var SignatoryInterface
     */
    private $signatory;

    /**
     * @var ChannelInterface[]
     */
    private $channels = [];

    /**
     * @var UnaryCallResolverInterface
     */
    private $unaryCallResolver;

    /**
     * Client constructor.
     * @param UserContextInterface $user
     * @param SignatoryInterface $signatory
     * @param ChannelFactoryInterface $channelFactory
     * @param UnaryCallResolverInterface|null $unaryCallResolver
     */
    public function __construct(
        UserContextInterface $user,
        SignatoryInterface $signatory,
        ChannelFactoryInterface $channelFactory,
        UnaryCallResolverInterface $unaryCallResolver = null
    ) {
        $this->user = $user;
        $this->signatory = $signatory;
        $this->channelFactory = $channelFactory;
        $this->unaryCallResolver = $unaryCallResolver ?: new UnaryCallResolver();
    }

    /**
     * @param string $name
     * @return ChannelInterface
     */
    public function getChannel(string $name): ChannelInterface
    {
        if (!\array_key_exists($name, $this->channels)) {
            $this->channels[$name] = $this->channelFactory->create($name, $this, $this->user);
        }

        return $this->channels[$name];
    }

    /**
     * @param Proposal $proposal
     * @param TransactionOptions|null $options
     * @return ResponseCollection
     */
    public function processProposal(
        Proposal $proposal,
        TransactionOptions $options
    ): ResponseCollection {
        $privateKey = $this->user->getOrganization()->getPrivateKey();

        $signedProposal = $this->signatory->signProposal($proposal, new \SplFileObject($privateKey));

        return $this->processSignedProposal($signedProposal, $options);
    }

    /**
     * The SignedProposal instances is asynchronously transmitted to Peers. This method
     * waits until all Responses are collected and returns the ResponseCollection.
     *
     * Each Response in the Collection wraps a ProposalResponse upon success, or an Exception upon failure.
     *
     * @param SignedProposal $proposal
     * @param TransactionOptions|null $options
     * @return ResponseCollection
     * @throws RuntimeException
     */
    private function processSignedProposal(
        SignedProposal $proposal,
        TransactionOptions $options
    ): ResponseCollection {
        if (!$options->hasPeers()) {
            throw new RuntimeException('Could not determine peers for this transaction');
        }

        // Convert peers into asynchronous calls.
        $calls = \array_map(function (PeerInterface $peer) use ($proposal) {
            return $peer->processSignedProposal($proposal);
        }, $options->getPeers());

        // Resolve calls to responses.
        return $this->unaryCallResolver->resolveMany(...$calls);
    }
}
