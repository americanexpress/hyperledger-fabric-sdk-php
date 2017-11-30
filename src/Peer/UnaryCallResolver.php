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

use AmericanExpress\HyperledgerFabricClient\Exception\RuntimeException;
use AmericanExpress\HyperledgerFabricClient\Proposal\Response;
use AmericanExpress\HyperledgerFabricClient\Proposal\ResponseCollection;
use Grpc\UnaryCall;
use Hyperledger\Fabric\Protos\Peer\ProposalResponse;
use function igorw\get_in;

final class UnaryCallResolver implements UnaryCallResolverInterface
{
    /**
     * @param UnaryCall $call
     * @return Response
     */
    public function resolveOne(UnaryCall $call): Response
    {
        [$proposalResponse, $status] = $call->wait();

        if ($proposalResponse instanceof ProposalResponse) {
            return Response::fromProposalResponse($proposalResponse);
        }

        $status = (array) $status;
        return Response::fromException(
            new RuntimeException(get_in($status, ['details']), get_in($status, ['code']))
        );
    }

    /**
     * @param UnaryCall|UnaryCall[] ...$calls
     * @return ResponseCollection
     */
    public function resolveMany(UnaryCall ...$calls): ResponseCollection
    {
        $peer = $this;

        return new ResponseCollection(\array_map(function (UnaryCall $call) use ($peer) {
            return $peer->resolveOne($call);
        }, $calls));
    }
}
