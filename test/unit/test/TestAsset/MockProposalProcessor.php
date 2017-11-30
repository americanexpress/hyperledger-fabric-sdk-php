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

namespace AmericanExpressTest\HyperledgerFabricClient\TestAsset;

use AmericanExpress\HyperledgerFabricClient\Proposal\ResponseCollection;
use AmericanExpress\HyperledgerFabricClient\Proposal\ProposalProcessorInterface;
use AmericanExpress\HyperledgerFabricClient\Transaction\TransactionOptions;
use Hyperledger\Fabric\Protos\Peer\Proposal;

class MockProposalProcessor implements ProposalProcessorInterface
{
    /**
     * @var Proposal $proposal
     */
    private $proposal;
    /**
     * @var TransactionOptions $options
     */
    private $options;

    /**
     * @var ResponseCollection $returnValue
     */
    private $returnValue;

    /**
     * MockProposalProcessor constructor.
     * @param ResponseCollection $returnValue
     */
    public function __construct(ResponseCollection $returnValue = null)
    {
        $this->returnValue = $returnValue !== null ? $returnValue : new ResponseCollection();
    }

    /**
     * @param Proposal $proposal
     * @param TransactionOptions $options
     * @return ResponseCollection
     */
    public function processProposal(Proposal $proposal, TransactionOptions $options): ResponseCollection
    {
        $this->proposal = $proposal;
        $this->options = $options;

        return $this->returnValue;
    }

    /**
     * @return Proposal
     */
    public function getProposal()
    {
        return $this->proposal;
    }

    /**
     * @return TransactionOptions
     */
    public function getTransactionOptions()
    {
        return $this->options;
    }
}
