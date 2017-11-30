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

class Response
{
    /**
     * @var ProposalResponse|\Exception
     */
    private $response;

    /**
     * ProposalResponse constructor.
     * @param mixed $response
     */
    private function __construct($response)
    {
        $this->response = $response;
    }

    /**
     * @param ProposalResponse $response
     * @return Response
     */
    public static function fromProposalResponse(ProposalResponse $response): Response
    {
        return new Response($response);
    }

    /**
     * @param \Exception $exception
     * @return Response
     */
    public static function fromException(\Exception $exception): Response
    {
        return new Response($exception);
    }

    /**
     * @return ProposalResponse|null
     */
    public function getProposalResponse(): ?ProposalResponse
    {
        return $this->isException() ? null : $this->response;
    }

    /**
     * @return \Exception|null
     */
    public function getException(): ?\Exception
    {
        return $this->isException() ? $this->response : null;
    }

    /**
     * @return bool
     */
    public function isException(): bool
    {
        return $this->response instanceof \Exception;
    }
}
