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

use AmericanExpress\HyperledgerFabricClient\Channel\Channel;
use AmericanExpress\HyperledgerFabricClient\Channel\ChannelInterface;
use AmericanExpress\HyperledgerFabricClient\Channel\ChannelProposalProcessorInterface;
use AmericanExpress\HyperledgerFabricClient\Channel\ChannelProviderInterface;
use AmericanExpress\HyperledgerFabricClient\EndorserClient\EndorserClientManagerInterface;
use AmericanExpress\HyperledgerFabricClient\Peer\Peer;
use AmericanExpress\HyperledgerFabricClient\Peer\PeerOptionsInterface;
use AmericanExpress\HyperledgerFabricClient\Proposal\ResponseCollection;
use AmericanExpress\HyperledgerFabricClient\ProtoFactory\HeaderFactory;
use AmericanExpress\HyperledgerFabricClient\ProtoFactory\ProposalFactory;
use AmericanExpress\HyperledgerFabricClient\ProtoFactory\SignatureHeaderFactory;
use AmericanExpress\HyperledgerFabricClient\Signatory\SignatoryInterface;
use AmericanExpress\HyperledgerFabricClient\Transaction\TransactionIdentifierGeneratorInterface;
use AmericanExpress\HyperledgerFabricClient\Transaction\TransactionOptions;
use AmericanExpress\HyperledgerFabricClient\User\UserContextInterface;
use Hyperledger\Fabric\Protos\Common\ChannelHeader;
use Hyperledger\Fabric\Protos\Common\Header;
use Hyperledger\Fabric\Protos\Peer\Proposal;
use Hyperledger\Fabric\Protos\Peer\SignedProposal;

final class Client implements ChannelProviderInterface, ChannelProposalProcessorInterface
{
    /**
     * @var UserContextInterface
     */
    private $user;

    /**
     * @var EndorserClientManagerInterface
     */
    private $endorserClients;

    /**
     * @var SignatoryInterface
     */
    private $signatory;

    /**
     * @var ChannelInterface[]
     */
    private $channels = [];

    /**
     * @var TransactionIdentifierGeneratorInterface
     */
    private $transactionIdGenerator;

    /**
     * @var int
     */
    private $epoch;

    /**
     * Client constructor.
     * @param UserContextInterface $user
     * @param SignatoryInterface $signatory
     * @param EndorserClientManagerInterface $endorserClients
     * @param TransactionIdentifierGeneratorInterface $transactionIdGenerator
     * @param int $epoch
     */
    public function __construct(
        UserContextInterface $user,
        SignatoryInterface $signatory,
        EndorserClientManagerInterface $endorserClients,
        TransactionIdentifierGeneratorInterface $transactionIdGenerator,
        int $epoch = 0
    ) {
        $this->user = $user;
        $this->signatory = $signatory;
        $this->endorserClients = $endorserClients;
        $this->transactionIdGenerator = $transactionIdGenerator;
        $this->epoch = $epoch;
    }

    /**
     * @param string $name
     * @return ChannelInterface
     */
    public function getChannel(string $name): ChannelInterface
    {
        if (!\array_key_exists($name, $this->channels)) {
            $this->channels[$name] = new Channel($name, $this);
        }

        return $this->channels[$name];
    }

    /**
     * @param Proposal $proposal
     * @param TransactionOptions|null $options
     * @return ResponseCollection
     */
    private function processProposal(
        Proposal $proposal,
        TransactionOptions $options = null
    ): ResponseCollection {
        $privateKey = $this->user->getOrganization()->getPrivateKey();

        $signedProposal = $this->signatory->signProposal($proposal, new \SplFileObject($privateKey));

        return $this->processSignedProposal($signedProposal, $options);
    }

    /**
     * @param SignedProposal $proposal
     * @param TransactionOptions|null $options
     * @return ResponseCollection
     */
    private function processSignedProposal(
        SignedProposal $proposal,
        TransactionOptions $options = null
    ): ResponseCollection {
        if ($options && $options->hasPeers()) {
            $peerOptionsCollection = $options->getPeers();
        } else {
            $peerOptionsCollection = $this->user->getOrganization()->getPeers();
        }

        $endorserClients = $this->endorserClients;

        $peers = array_map(function (PeerOptionsInterface $peerOptions) use ($endorserClients) {
            return new Peer($peerOptions, $endorserClients);
        }, $peerOptionsCollection);

        return new ResponseCollection(array_map(function (Peer $peer) use ($proposal) {
            return $peer->processSignedProposal($proposal);
        }, $peers));
    }

    /**
     * @param ChannelHeader $channelHeader
     * @return Header
     */
    private function createHeaderFromChannelHeader(ChannelHeader $channelHeader): Header
    {
        $identity = $this->user->getIdentity();
        $transactionId = $this->transactionIdGenerator->fromSerializedIdentity($identity);
        $channelHeader->setTxId($transactionId->getId());
        $channelHeader->setEpoch($this->epoch);
        $signatureHeader = SignatureHeaderFactory::create(
            $identity,
            $transactionId->getNonce()
        );
        return HeaderFactory::create($channelHeader, $signatureHeader);
    }

    /**
     * @param ChannelHeader $channelHeader
     * @param string $payload
     * @param TransactionOptions|null $options
     * @return ResponseCollection
     */
    public function processChannelProposal(
        ChannelHeader $channelHeader,
        string $payload,
        TransactionOptions $options = null
    ): ResponseCollection {
        $header = $this->createHeaderFromChannelHeader($channelHeader);
        $proposal = ProposalFactory::create($header, $payload);

        return $this->processProposal($proposal, $options);
    }
}
