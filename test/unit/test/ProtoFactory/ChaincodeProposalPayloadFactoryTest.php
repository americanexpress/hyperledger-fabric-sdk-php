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

namespace AmericanExpressTest\HyperledgerFabricClient\ProtoFactory;

use AmericanExpress\HyperledgerFabricClient\ProtoFactory\ChaincodeInvocationSpecFactory;
use AmericanExpress\HyperledgerFabricClient\ProtoFactory\ChaincodeProposalPayloadFactory;
use Hyperledger\Fabric\Protos\Peer\ChaincodeProposalPayload;
use PHPUnit\Framework\TestCase;

/**
 * @covers \AmericanExpress\HyperledgerFabricClient\ProtoFactory\ChaincodeProposalPayloadFactory
 */
class ChaincodeProposalPayloadFactoryTest extends TestCase
{
    public function testFromChaincodeInvocationSpec()
    {
        $chaincodeInvocationSpec = ChaincodeInvocationSpecFactory::fromArgs([
            'foo',
            'bar',
        ]);

        $result = ChaincodeProposalPayloadFactory::fromChaincodeInvocationSpec($chaincodeInvocationSpec);

        self::assertInstanceOf(ChaincodeProposalPayload::class, $result);
        $expected = <<<'TAG'



foo
bar
TAG;
        self::assertSame($expected, $result->getInput());
    }

    public function testFromChaincodeInvocationSpecArgs()
    {
        $result = ChaincodeProposalPayloadFactory::fromChaincodeInvocationSpecArgs([
            'foo',
            'bar',
        ]);

        self::assertInstanceOf(ChaincodeProposalPayload::class, $result);
        $expected = <<<'TAG'



foo
bar
TAG;
        self::assertSame($expected, $result->getInput());
    }
}
