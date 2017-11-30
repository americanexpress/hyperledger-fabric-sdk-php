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

use AmericanExpress\HyperledgerFabricClient\Chaincode\Chaincode;
use AmericanExpress\HyperledgerFabricClient\Chaincode\ChaincodeProposalProcessorInterface;
use AmericanExpress\HyperledgerFabricClient\Exception\InvalidArgumentException;
use AmericanExpress\HyperledgerFabricClient\Exception\RuntimeException;
use AmericanExpress\HyperledgerFabricClient\Peer\PeerInterface;
use AmericanExpress\HyperledgerFabricClient\Peer\PeerOptionsInterface;
use AmericanExpress\HyperledgerFabricClient\Proposal\ResponseCollection;
use AmericanExpress\HyperledgerFabricClient\Proposal\ProposalProcessorInterface;
use AmericanExpress\HyperledgerFabricClient\ProtoFactory\ChannelHeaderFactory;
use AmericanExpress\HyperledgerFabricClient\ProtoFactory\ProposalFactory;
use AmericanExpress\HyperledgerFabricClient\Transaction\TransactionOptions;
use AmericanExpress\HyperledgerFabricClient\Identity\SerializedIdentityAwareHeaderGeneratorInterface;
use Assert\Assertion;
use Assert\AssertionFailedException;
use Hyperledger\Fabric\Protos\Peer\ChaincodeHeaderExtension;
use Hyperledger\Fabric\Protos\Peer\ChaincodeID;
use Hyperledger\Fabric\Protos\Peer\ChaincodeProposalPayload;

final class Channel implements ChannelInterface, ChaincodeProposalProcessorInterface
{
    /**
     * @var string
     */
    private $name;

    /**
     * @var ProposalProcessorInterface
     */
    private $client;

    /**
     * @var PeerInterface[] $peers
     */
    private $peers;

    /**
     * @var SerializedIdentityAwareHeaderGeneratorInterface
     */
    private $headerGenerator;

    /**
     * @param string $name
     * @param ProposalProcessorInterface $client
     * @param SerializedIdentityAwareHeaderGeneratorInterface $userAwareHeaderGenerator
     * @param PeerOptionsInterface[] $peers
     * @throws InvalidArgumentException
     */
    public function __construct(
        string $name,
        ProposalProcessorInterface $client,
        SerializedIdentityAwareHeaderGeneratorInterface $userAwareHeaderGenerator,
        array $peers = []
    ) {
        try {
            Assertion::allIsInstanceOf($peers, PeerInterface::class);
        } catch (AssertionFailedException $e) {
            throw new InvalidArgumentException(
                sprintf('Failed to create Channel `%s` due to invalid Peer collection.', $name),
                0,
                $e
            );
        }

        $this->name = $name;
        $this->client = $client;
        $this->headerGenerator = $userAwareHeaderGenerator;
        $this->peers = $peers;
    }

    /**
     * @param TransactionOptions $request
     * @param ChaincodeID $chaincodeId
     * @param mixed[] $args
     * @return ResponseCollection
     */
    public function queryByChainCode(
        TransactionOptions $request,
        ChaincodeID $chaincodeId,
        array $args = []
    ): ResponseCollection {
        $chainCode = $this->getChaincode([
            'name' => $chaincodeId->getName(),
            'version' => $chaincodeId->getPath(),
            'path' => $chaincodeId->getVersion(),
        ]);

        $functionName = array_shift($args);
        $args[] = $request;

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
     * @param PeerInterface[] ...$peers
     * @return void
     */
    public function addPeers(PeerInterface ...$peers): void
    {
        $this->peers = array_merge($this->peers, $peers);
    }

    /**
     * @param PeerInterface[] $peers
     * @return void
     */
    public function setPeers(array $peers): void
    {
        $this->peers = [];
        $this->addPeers(...$peers);
    }

    /**
     * @return PeerInterface[]
     */
    public function getPeers(): array
    {
        return $this->peers;
    }

    /**
     * @param TransactionOptions|null $options
     * @return PeerInterface[]
     */
    private function resolvePeers(TransactionOptions $options): array
    {
        if ($options->hasPeers()) {
            return $options->getPeers();
        }

        return $this->getPeers();
    }

    /**
     * @param TransactionOptions|null $options
     * @return TransactionOptions
     */
    private function normalizeTransactionOptions(TransactionOptions $options = null): TransactionOptions
    {
        if ($options === null) {
            $options = new TransactionOptions();
        }

        return $options->withPeers(...$this->resolvePeers($options));
    }

    /**
     *
     * Envelopes a Chaincode function invocation from a Chaincode object
     *
     * @param ChaincodeProposalPayload $payload
     * @param ChaincodeHeaderExtension $extension
     * @param TransactionOptions|null $options
     * @return ResponseCollection
     * @throws RuntimeException
     */
    public function processChaincodeProposal(
        ChaincodeProposalPayload $payload,
        ChaincodeHeaderExtension $extension,
        TransactionOptions $options = null
    ): ResponseCollection {
        $channelHeader = ChannelHeaderFactory::create($this->name);
        $channelHeader->setExtension($extension->serializeToString());

        $options = $this->normalizeTransactionOptions($options);
        if (!$options->hasPeers()) {
            throw new RuntimeException('Could not determine peers for this transaction');
        }

        $header = $this->headerGenerator->generateHeader($channelHeader);
        $proposal = ProposalFactory::create($header, $payload->serializeToString());

        return $this->client->processProposal($proposal, $options);
    }
}
