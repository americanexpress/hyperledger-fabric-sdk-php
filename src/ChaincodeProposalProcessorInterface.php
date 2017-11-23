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

use AmericanExpress\HyperledgerFabricClient\Transaction\TransactionRequest;
use Hyperledger\Fabric\Protos\Peer\ChaincodeHeaderExtension;
use Hyperledger\Fabric\Protos\Peer\ChaincodeProposalPayload;
use Hyperledger\Fabric\Protos\Peer\ProposalResponse;

interface ChaincodeProposalProcessorInterface
{
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
    ): ProposalResponse;
}