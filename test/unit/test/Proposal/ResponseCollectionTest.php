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

namespace AmericanExpressTest\HyperledgerFabricClient\Proposal;

use AmericanExpress\HyperledgerFabricClient\Proposal\Response;
use AmericanExpress\HyperledgerFabricClient\Proposal\ResponseCollection;
use Hyperledger\Fabric\Protos\Peer\ProposalResponse;
use PHPUnit\Framework\TestCase;

/**
 * @covers \AmericanExpress\HyperledgerFabricClient\Proposal\ResponseCollection
 */
class ResponseCollectionTest extends TestCase
{
    public function testGetDefaults()
    {
        $sut = new ResponseCollection();

        self::assertFalse($sut->hasProposalResponses());
        self::assertCount(0, $sut->getProposalResponses());

        self::assertFalse($sut->hasExceptions());
        self::assertCount(0, $sut->getExceptions());
    }

    public function testGetResponses()
    {
        $sut = new ResponseCollection([
            Response::fromException(new \Exception()),
            Response::fromProposalResponse(new ProposalResponse()),
            Response::fromException(new \Exception()),
            Response::fromProposalResponse(new ProposalResponse()),
        ]);

        self::assertTrue($sut->hasProposalResponses());
        self::assertCount(2, $sut->getProposalResponses());

        self::assertTrue($sut->hasExceptions());
        self::assertCount(2, $sut->getExceptions());
    }
}
