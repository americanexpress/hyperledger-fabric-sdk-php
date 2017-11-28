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

namespace AmericanExpress\HyperledgerFabricClient\Peer;

use AmericanExpress\HyperledgerFabricClient\EndorserClient\EndorserClientManager;
use AmericanExpress\HyperledgerFabricClient\EndorserClient\EndorserClientManagerInterface;
use AmericanExpress\HyperledgerFabricClient\Exception\RuntimeException;
use AmericanExpress\HyperledgerFabricClient\Exception\UnexpectedValueException;
use Assert\Assertion;
use Assert\AssertionFailedException;
use Grpc\UnaryCall;
use Hyperledger\Fabric\Protos\Peer\ProposalResponse;
use Hyperledger\Fabric\Protos\Peer\SignedProposal;
use function igorw\get_in;

class Peer implements PeerInterface
{
    /**
     * @var PeerOptionsInterface
     */
    private $options;

    /**
     * @var EndorserClientManagerInterface
     */
    private $endorserClients;

    /**
     * Peer constructor.
     * @param PeerOptionsInterface $options
     * @param EndorserClientManagerInterface|null $endorserClients
     */
    public function __construct(PeerOptionsInterface $options, EndorserClientManagerInterface $endorserClients = null)
    {
        $this->options = $options;
        $this->endorserClients = $endorserClients ?: new EndorserClientManager();
    }

    /**
     * @param SignedProposal $proposal
     * @return ProposalResponse
     * @throws RuntimeException
     * @throws UnexpectedValueException
     */
    public function processSignedProposal(SignedProposal $proposal): ProposalResponse {
        $host = $this->options->getRequests();

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
