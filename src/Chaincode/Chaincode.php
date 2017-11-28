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

namespace AmericanExpress\HyperledgerFabricClient\Chaincode;

use AmericanExpress\HyperledgerFabricClient\Exception\InvalidArgumentException;
use AmericanExpress\HyperledgerFabricClient\ProtoFactory\ChaincodeHeaderExtensionFactory;
use AmericanExpress\HyperledgerFabricClient\ProtoFactory\ChaincodeIdFactory;
use AmericanExpress\HyperledgerFabricClient\ProtoFactory\ChaincodeProposalPayloadFactory;
use AmericanExpress\HyperledgerFabricClient\Transaction\TransactionOptions;
use Hyperledger\Fabric\Protos\Peer\ProposalResponse;

class Chaincode
{
    /**
     * @var string $name
     */
    private $name;

    /**
     * @var string $version
     */
    private $version = '';

    /**
     * @var string $path
     */
    private $path = '';

    /**
     * @var ChaincodeProposalProcessorInterface $channel
     */
    private $channel;

    /**
     * Chaincode constructor.
     * @param string | array $nameOrDetails
     * @param ChaincodeProposalProcessorInterface $channel
     * @throws InvalidArgumentException
     */
    public function __construct($nameOrDetails, ChaincodeProposalProcessorInterface $channel)
    {
        $details = $this->normalizeName($nameOrDetails);

        if (empty($details['name'])) {
            throw new InvalidArgumentException('A string name parameter must be provided');
        }

        $this->name = $details['name'];
        $this->version = $details['version'];
        $this->path = $details['path'];
        $this->channel = $channel;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getVersion(): string
    {
        return $this->version;
    }

    /**
     * @return string
     */
    public function getPath(): string
    {
        return $this->path;
    }

    /**
     * @param string | array $nameOrDetails
     * @return array
     * @throws InvalidArgumentException
     */
    private function normalizeName($nameOrDetails)
    {
        if (\is_string($nameOrDetails)) {
            return ['name' => $nameOrDetails, 'version' => '', 'path' => ''];
        }

        if (!\is_array($nameOrDetails)) {
            throw new InvalidArgumentException('name must be a string or an array');
        }

        return \array_merge(['name' => '', 'version' => '', 'path' => ''], $nameOrDetails);
    }

    /**
     * @param mixed $value
     * @param mixed[] $array
     * @return mixed[]
     */
    private function prependValueToArray($value, array $array): array
    {
        array_unshift($array, $value);
        return $array;
    }

    /**
     * @param string $name
     * @param mixed[] $args
     * @param TransactionOptions|null $options
     * @return ProposalResponse
     */
    private function executeCommand(
        string $name,
        array $args = [],
        TransactionOptions $options = null
    ): ProposalResponse {
        $chaincodeId = ChaincodeIdFactory::create(
            $this->path,
            $this->name,
            $this->version
        );

        $chaincodeHeaderExtension = ChaincodeHeaderExtensionFactory::fromChaincodeId($chaincodeId);

        $nameAndArguments = $this->prependValueToArray($name, $args);
        $chaincodeProposalPayload = ChaincodeProposalPayloadFactory::fromChaincodeInvocationSpecArgs(
            $nameAndArguments
        );

        return $this->channel->processChaincodeProposal(
            $chaincodeProposalPayload,
            $chaincodeHeaderExtension,
            $options
        );
    }

    /**
     * @param mixed[] $arguments
     * @return TransactionOptions|null
     */
    private function extractTransactionOptions(array $arguments): ?TransactionOptions
    {
        $transactionRequest = null;
        if (count($arguments) > 0) {
            $lastArgumentIndex = count($arguments) - 1;
            $lastArgument = $arguments[$lastArgumentIndex];

            if ($lastArgument instanceof TransactionOptions) {
                $transactionRequest = $lastArgument;
            }
        }

        return $transactionRequest;
    }

    /**
     *
     * Execute a proposal against a Chaincode. Because Chaincodes have variable methods, __call allows for dynamic
     * function submission
     *
     * @param string $name
     * @param array $arguments
     * @return \Hyperledger\Fabric\Protos\Peer\ProposalResponse
     */
    public function __call(string $name, array $arguments = []): ProposalResponse
    {
        $options = $this->extractTransactionOptions($arguments);
        if ($options !== null) {
            array_pop($arguments);
        }

        return $this->executeCommand($name, $arguments, $options);
    }

    /**
     *
     * Typing the default call to a Chaincode (invoke)
     *
     * @param array ...$args
     * @return \Hyperledger\Fabric\Protos\Peer\ProposalResponse
     */
    public function invoke(...$args)
    {
        return $this->__call('invoke', $args);
    }
}
