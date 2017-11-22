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

use AmericanExpress\HyperledgerFabricClient\Client\ClientInterface;
use AmericanExpress\HyperledgerFabricClient\ProtoFactory\ChaincodeProposalPayloadFactory;
use AmericanExpress\HyperledgerFabricClient\ProtoFactory\ChannelHeaderFactory;
use AmericanExpress\HyperledgerFabricClient\ProtoFactory\HeaderFactory;
use AmericanExpress\HyperledgerFabricClient\ProtoFactory\ProposalFactory;
use AmericanExpress\HyperledgerFabricClient\Transaction\TransactionContextFactoryInterface;
use AmericanExpress\HyperledgerFabricClient\Transaction\TransactionRequest;
use Hyperledger\Fabric\Protos\Peer\Proposal;
use Hyperledger\Fabric\Protos\Peer\ProposalResponse;

final class Channel implements ChannelInterface
{
    /**
     * @var string
     */
    private $name;

    /**
     * @var ClientInterface
     */
    private $client;

    /**
     * @var TransactionContextFactoryInterface
     */
    private $transactionContextFactory;

    /**
     * @param string $name
     * @param ClientInterface $client
     * @param TransactionContextFactoryInterface $transactionContextFactory
     */
    public function __construct(
        string $name,
        ClientInterface $client,
        TransactionContextFactoryInterface $transactionContextFactory
    ) {
        $this->name = $name;
        $this->client = $client;
        $this->transactionContextFactory = $transactionContextFactory;
    }

    /**
     * @param TransactionRequest $request
     * @return ProposalResponse
     */
    public function queryByChainCode(TransactionRequest $request): ProposalResponse {
        $proposal = $this->createProposal($request);

        return $this->client->processProposal($proposal, $request);
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
            $this->name,
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
}
