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

namespace AmericanExpress\HyperledgerFabricClient\Proposal;

use Hyperledger\Fabric\Protos\Peer\ProposalResponse;

class ResponseCollection
{
    /**
     * @var Response[]
     */
    private $responses;

    /**
     * ProposalResponseCollection constructor.
     * @param mixed[] $responses
     */
    public function __construct(array $responses = [])
    {
        $this->responses = $responses;
    }

    /**
     * @return ProposalResponse[]
     */
    public function getProposalResponses(): array
    {
        return \array_filter(\array_map(function (Response $response) {
            return $response->getProposalResponse();
        }, $this->responses));
    }

    /**
     * @return bool
     */
    public function hasProposalResponses(): bool
    {
        return \count($this->getProposalResponses()) > 0;
    }

    /**
     * @return \Exception[]
     */
    public function getExceptions(): array
    {
        return \array_filter(\array_map(function (Response $response) {
            return $response->getException();
        }, $this->responses));
    }

    /**
     * @return bool
     */
    public function hasExceptions(): bool
    {
        return \count($this->getExceptions()) > 0;
    }
}
