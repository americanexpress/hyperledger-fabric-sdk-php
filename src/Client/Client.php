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

use AmericanExpress\HyperledgerFabricClient\Channel;
use AmericanExpress\HyperledgerFabricClient\ChannelFactory;
use AmericanExpress\HyperledgerFabricClient\ChannelInterface;
use AmericanExpress\HyperledgerFabricClient\ChannelProposalProcessorInterface;
use AmericanExpress\HyperledgerFabricClient\Config\ClientConfigInterface;
use AmericanExpress\HyperledgerFabricClient\EndorserClientManagerInterface;
use AmericanExpress\HyperledgerFabricClient\Exception\RuntimeException;
use AmericanExpress\HyperledgerFabricClient\Exception\UnexpectedValueException;
use AmericanExpress\HyperledgerFabricClient\ProtoFactory\HeaderFactory;
use AmericanExpress\HyperledgerFabricClient\ProtoFactory\ProposalFactory;
use AmericanExpress\HyperledgerFabricClient\ProtoFactory\SignatureHeaderFactory;
use AmericanExpress\HyperledgerFabricClient\Signatory\SignatoryInterface;
use AmericanExpress\HyperledgerFabricClient\Transaction\TransactionContext;
use AmericanExpress\HyperledgerFabricClient\Transaction\TransactionContextFactoryInterface;
use AmericanExpress\HyperledgerFabricClient\Transaction\TransactionRequest;
use AmericanExpress\HyperledgerFabricClient\User\UserContextInterface;
use Assert\Assertion;
use Assert\AssertionFailedException;
use Grpc\UnaryCall;
use Hyperledger\Fabric\Protos\Common\ChannelHeader;
use Hyperledger\Fabric\Protos\Common\Header;
use Hyperledger\Fabric\Protos\Common\SignatureHeader;
use Hyperledger\Fabric\Protos\MSP\SerializedIdentity;
use Hyperledger\Fabric\Protos\Peer\Proposal;
use Hyperledger\Fabric\Protos\Peer\ProposalResponse;
use Hyperledger\Fabric\Protos\Peer\SignedProposal;
use function igorw\get_in;

final class Client implements ClientInterface, ChannelProposalProcessorInterface
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
     * @var ClientConfigInterface
     */
    private $config;

    /**
     * @var ChannelInterface[]
     */
    private $channels = [];

    /**
     * @var TransactionContextFactoryInterface
     */
    private $transactionContextFactory;

    /**
     * Client constructor.
     * @param UserContextInterface $user
     * @param SignatoryInterface $signatory
     * @param EndorserClientManagerInterface $endorserClients
     * @param ClientConfigInterface $config
     * @param TransactionContextFactoryInterface $transactionContextFactory
     */
    public function __construct(
        UserContextInterface $user,
        SignatoryInterface $signatory,
        EndorserClientManagerInterface $endorserClients,
        ClientConfigInterface $config,
        TransactionContextFactoryInterface $transactionContextFactory
    ) {
        $this->user = $user;
        $this->signatory = $signatory;
        $this->endorserClients = $endorserClients;
        $this->config = $config;
        $this->transactionContextFactory = $transactionContextFactory;
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
     * @param TransactionRequest|null $context
     * @return ProposalResponse
     * @throws UnexpectedValueException
     * @throws RuntimeException
     */
    public function processProposal(Proposal $proposal, TransactionRequest $context = null): ProposalResponse
    {
        $privateKey = $this->user->getOrganization()->getPrivateKey();

        $signedProposal = $this->signatory->signProposal($proposal, new \SplFileObject($privateKey));

        return $this->processSignedProposal($signedProposal, $context);
    }

    /**
     * @param SignedProposal $proposal
     * @param TransactionRequest|null $context
     * @return ProposalResponse
     * @throws RuntimeException
     * @throws UnexpectedValueException
     */
    private function processSignedProposal(
        SignedProposal $proposal,
        TransactionRequest $context = null
    ): ProposalResponse {

        if ($context && $context->hasPeer()) {
            $peer = $context->getPeer();
        } else {
            $peer = $this->user->getOrganization()->getDefaultPeer();
        }

        $endorserClient = $this->endorserClients->get($peer->getRequests());

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

    /**
     * @return TransactionContext
     */
    public function createTransactionContext(): TransactionContext
    {
        $identity = $this->user->getIdentity();

        return $this->transactionContextFactory->fromSerializedIdentity($identity);
    }

    /**
     * @param ChannelHeader $channelHeader
     * @return Header
     */
    private function createHeaderFromChannelHeader(ChannelHeader $channelHeader): Header
    {
        $transactionContext = $this->createTransactionContext();
        $channelHeader->setTxId($transactionContext->getTxId());
        $channelHeader->setEpoch($transactionContext->getEpoch());
        $signatureHeader = SignatureHeaderFactory::create(
            $transactionContext->getSerializedIdentity(),
            $transactionContext->getNonce()
        );
        return HeaderFactory::create($channelHeader, $signatureHeader);
    }

    /**
     * @param ChannelHeader $channelHeader
     * @param string $payload
     * @param TransactionRequest|null $request
     * @return ProposalResponse
     */
    public function processChannelProposal(
        ChannelHeader $channelHeader,
        string $payload,
        TransactionRequest $request = null
    ): ProposalResponse {
        $header = $this->createHeaderFromChannelHeader($channelHeader);
        $proposal = ProposalFactory::create($header, $payload);

        return $this->processProposal($proposal, $request);

    }
}
