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
use AmericanExpress\HyperledgerFabricClient\Transaction\TransactionRequest;
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
     * @param TransactionRequest $request
     * @return ProposalResponse
     */
    public function queryByChainCode(TransactionRequest $request): ProposalResponse {
        $proposal = $this->createProposal($request);

        return $this->processProposal($proposal, $request);
    }

    /**
     * @param TransactionRequest $request
     * @return Proposal
     */
    private function createProposal(TransactionRequest $request): Proposal
    {
        $transactionContext = $this->transactionContextFactory->fromTransactionRequest($request);

        $chainHeader = ChannelHeaderFactory::create(
            $transactionContext,
            $request->getChannelId(),
            $request->getChaincodeId()->getPath(),
            $request->getChaincodeId()->getName(),
            $request->getChaincodeId()->getVersion()
        );

        $chaincodeProposalPayload = ChaincodeProposalPayloadFactory::fromChaincodeInvocationSpecArgs(
            $request->getArgs()
        );

        $header = HeaderFactory::fromTransactionContext($transactionContext, $chainHeader);

        return ProposalFactory::create($header, $chaincodeProposalPayload);
    }

    /**
     * @param Proposal $proposal
     * @param TransactionRequest $request
     * @return ProposalResponse
     */
    private function processProposal(Proposal $proposal, TransactionRequest $request): ProposalResponse {
        $privateKey = $request->getOrganization()->getPrivateKey();

        $signedProposal = $this->signatory->signProposal($proposal, new \SplFileObject($privateKey));

        return $this->processSignedProposal($signedProposal, $request);
    }

    /**
     * @param SignedProposal $signedProposal
     * @param TransactionRequest $request
     * @return ProposalResponse
     */
    private function processSignedProposal(
        SignedProposal $signedProposal,
        TransactionRequest $request
    ): ProposalResponse {
        $endorserClient = $this->endorserClients->get($request->getPeerOptions()->getRequests());

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
