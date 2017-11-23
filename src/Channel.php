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
use AmericanExpress\HyperledgerFabricClient\ProtoFactory\ChannelHeaderFactory;
use AmericanExpress\HyperledgerFabricClient\ProtoFactory\HeaderFactory;
use AmericanExpress\HyperledgerFabricClient\ProtoFactory\ProposalFactory;
use AmericanExpress\HyperledgerFabricClient\Transaction\TransactionContextFactoryInterface;
use AmericanExpress\HyperledgerFabricClient\Transaction\TransactionRequest;
use Hyperledger\Fabric\Protos\Peer\ChaincodeHeaderExtension;
use Hyperledger\Fabric\Protos\Peer\ChaincodeID;
use Hyperledger\Fabric\Protos\Peer\ChaincodeProposalPayload;
use Hyperledger\Fabric\Protos\Peer\ProposalResponse;

final class Channel implements ChannelInterface, ChaincodeProposalProcessorInterface
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
     * @param ChaincodeID $chaincodeId
     * @param mixed[] $args
     * @return ProposalResponse
     */
    public function queryByChainCode(
        TransactionRequest $request,
        ChaincodeID $chaincodeId,
        array $args = []
    ): ProposalResponse {
        $chainCode = $this->getChainCode([
            'name' => $chaincodeId->getName(),
            'version' => $chaincodeId->getPath(),
            'path' => $chaincodeId->getVersion(),
        ]);

        $functionName = array_shift($args);
        array_push($args, $request);

        return $chainCode->$functionName(...$args);
    }

    /**
     *
     * Returns a named Chaincode for a channel
     *
     * @param string | array $nameOrVersionedName
     * @return Chaincode
     */
    public function getChaincode($nameOrVersionedName): Chaincode
    {
        return new Chaincode($nameOrVersionedName, $this);
    }

    /**
     *
     * Envelopes a Chaincode function invocation from a Chaincode object
     *
     * @param ChaincodeProposalPayload $payload
     * @param ChaincodeHeaderExtension $extension
     * @param TransactionRequest|null $request
     * @return ProposalResponse
     */
    public function processChaincodeProposal(
        ChaincodeProposalPayload $payload,
        ChaincodeHeaderExtension $extension,
        TransactionRequest $request = null
    ): ProposalResponse {
        $transactionContext = $this->transactionContextFactory->fromTransactionRequest($request);
        $channelHeader = ChannelHeaderFactory::create($transactionContext, $this->name);
        $channelHeader->setExtension($extension->serializeToString());
        $header = HeaderFactory::fromTransactionContext($transactionContext, $channelHeader);

        return $this->client->processProposal(
            ProposalFactory::create($header, $payload),
            $request
        );
    }
}
