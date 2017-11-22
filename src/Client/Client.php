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

namespace AmericanExpress\HyperledgerFabricClient\Client;

use AmericanExpress\HyperledgerFabricClient\Channel\ChannelManagerInterface;
use AmericanExpress\HyperledgerFabricClient\ChannelInterface;
use AmericanExpress\HyperledgerFabricClient\EndorserClientManagerInterface;
use AmericanExpress\HyperledgerFabricClient\Exception\RuntimeException;
use AmericanExpress\HyperledgerFabricClient\Exception\UnexpectedValueException;
use AmericanExpress\HyperledgerFabricClient\Signatory\SignatoryInterface;
use AmericanExpress\HyperledgerFabricClient\Transaction\TransactionRequest;
use Assert\Assertion;
use Assert\AssertionFailedException;
use Grpc\UnaryCall;
use Hyperledger\Fabric\Protos\Peer\Proposal;
use Hyperledger\Fabric\Protos\Peer\ProposalResponse;
use Hyperledger\Fabric\Protos\Peer\SignedProposal;
use function igorw\get_in;

final class Client implements ClientInterface
{
    /**
     * @var ChannelManagerInterface
     */
    private $channels;

    /**
     * @var EndorserClientManagerInterface
     */
    private $endorserClients;

    /**
     * @var SignatoryInterface
     */
    private $signatory;

    /**
     * Client constructor.
     * @param SignatoryInterface $signatory
     * @param ChannelManagerInterface $channels
     * @param EndorserClientManagerInterface $endorserClients
     */
    public function __construct(
        SignatoryInterface $signatory = null,
        ChannelManagerInterface $channels = null,
        EndorserClientManagerInterface $endorserClients = null
    ) {
        $this->signatory = $signatory;
        $this->channels = $channels;
        $this->endorserClients = $endorserClients;
    }

    /**
     * @param string $name
     * @return ChannelInterface
     */
    public function getChannel(string $name): ChannelInterface
    {
        return $this->channels->get($name);
    }

    /**
     * @param Proposal $proposal
     * @param TransactionRequest $context
     * @return ProposalResponse
     * @throws \AmericanExpress\HyperledgerFabricClient\Exception\UnexpectedValueException
     * @throws \AmericanExpress\HyperledgerFabricClient\Exception\RuntimeException
     */
    public function processProposal(Proposal $proposal, TransactionRequest $context): ProposalResponse
    {
        $privateKey = $context->getOrganization()->getPrivateKey();

        $signedProposal = $this->signatory->signProposal($proposal, new \SplFileObject($privateKey));

        return $this->processSignedProposal($signedProposal, $context);
    }

    /**
     * @param SignedProposal $proposal
     * @param TransactionRequest $context
     * @return ProposalResponse
     * @throws \AmericanExpress\HyperledgerFabricClient\Exception\RuntimeException
     * @throws \AmericanExpress\HyperledgerFabricClient\Exception\UnexpectedValueException
     */
    private function processSignedProposal(
        SignedProposal $proposal,
        TransactionRequest $context
    ): ProposalResponse {
        $host = $context->getPeerOptions()->getRequests();

        $endorserClient = $this->endorserClients->get($host);

        $simpleSurfaceActiveCall = $endorserClient->ProcessProposal($proposal);

        try {
            Assertion::isInstanceOf($simpleSurfaceActiveCall, UnaryCall::class);
        } catch (AssertionFailedException $e) {
            throw UnexpectedValueException::fromException($e);
        }

        /** @var UnaryCall $simpleSurfaceActiveCall */
        [$proposalResponse, $status] = $simpleSurfaceActiveCall->wait();

        if ($proposalResponse instanceof ProposalResponse) {
            return $proposalResponse;
        }

        $status = (array) $status;
        throw new RuntimeException(get_in($status, ['details']), get_in($status, ['code']));
    }
}
