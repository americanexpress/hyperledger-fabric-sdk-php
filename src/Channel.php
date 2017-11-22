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

use AmericanExpress\HyperledgerFabricClient\Exception\RuntimeException;
use AmericanExpress\HyperledgerFabricClient\ProtoFactory\ChaincodeProposalPayloadFactory;
use AmericanExpress\HyperledgerFabricClient\ProtoFactory\ChannelHeaderFactory;
use AmericanExpress\HyperledgerFabricClient\ProtoFactory\HeaderFactory;
use AmericanExpress\HyperledgerFabricClient\ProtoFactory\ProposalFactory;
use AmericanExpress\HyperledgerFabricClient\Signatory\SignatoryInterface;
use AmericanExpress\HyperledgerFabricClient\Transaction\TransactionContextFactoryInterface;
use Grpc\UnaryCall;
use Hyperledger\Fabric\Protos\Peer\Proposal;
use Hyperledger\Fabric\Protos\Peer\ProposalResponse;
use Hyperledger\Fabric\Protos\Peer\SignedProposal;
use function igorw\get_in;

final class Channel implements ChannelInterface
{
    private $transactionContextFactory;

    /**
     * @var EndorserClientManagerInterface
     */
    private $endorserClients;

    /**
     * @var SignatoryInterface
     */
    private $signatory;

    /**
     * @param EndorserClientManagerInterface $endorserClients
     * @param TransactionContextFactoryInterface $transactionContextFactory
     * @param SignatoryInterface $signatory
     */
    public function __construct(
        EndorserClientManagerInterface $endorserClients,
        TransactionContextFactoryInterface $transactionContextFactory,
        SignatoryInterface $signatory
    ) {
        $this->endorserClients = $endorserClients;
        $this->transactionContextFactory = $transactionContextFactory;
        $this->signatory = $signatory;
    }

    /**
     * @param ChannelContext $context
     * @param ChaincodeQueryParams $params
     * @return ProposalResponse
     */
    public function queryByChainCode(
        ChannelContext $context,
        ChaincodeQueryParams $params
    ): ProposalResponse {
        $proposal = $this->createProposal($context, $params);

        return $this->processProposal($proposal, $context);
    }

    /**
     * @param ChannelContext $channelContext
     * @param ChaincodeQueryParams $params
     * @return Proposal
     */
    private function createProposal(ChannelContext $channelContext, ChaincodeQueryParams $params): Proposal
    {
        $transactionContext = $this->transactionContextFactory->fromChannelContext($channelContext);

        $chainHeader = ChannelHeaderFactory::create(
            $transactionContext,
            $params->getChannelId(),
            $params->getChaincodePath(),
            $params->getChaincodeName(),
            $params->getChaincodeVersion()
        );

        $chaincodeProposalPayload = ChaincodeProposalPayloadFactory::fromChaincodeInvocationSpecArgs(
            $params->getArgs()
        );

        $header = HeaderFactory::fromTransactionContext($transactionContext, $chainHeader);

        return ProposalFactory::create($header, $chaincodeProposalPayload);
    }

    /**
     * @param Proposal $proposal
     * @param ChannelContext $channelContext
     * @return ProposalResponse
     */
    private function processProposal(
        Proposal $proposal,
        ChannelContext $channelContext
    ): ProposalResponse {
        $privateKey = $channelContext->getPrivateKey();

        $signedProposal = $this->signatory->signProposal($proposal, $privateKey);

        return $this->processSignedProposal($signedProposal, $channelContext);
    }

    /**
     * @param SignedProposal $signedProposal
     * @param ChannelContext $channelContext
     * @return ProposalResponse
     */
    private function processSignedProposal(
        SignedProposal $signedProposal,
        ChannelContext $channelContext
    ): ProposalResponse {
        $endorserClient = $this->endorserClients->get($channelContext->getHost());

        /** @var UnaryCall $simpleSurfaceActiveCall */
        $simpleSurfaceActiveCall = $endorserClient->ProcessProposal($signedProposal);
        list($proposalResponse, $status) = $simpleSurfaceActiveCall->wait();
        $status = (array)$status;
        if ($proposalResponse instanceof ProposalResponse) {
            return $proposalResponse;
        }

        throw new RuntimeException(get_in($status, ['details']), get_in($status, ['code']));
    }
}
