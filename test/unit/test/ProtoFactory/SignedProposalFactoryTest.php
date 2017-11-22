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

use AmericanExpress\HyperledgerFabricClient\ProtoFactory\ChaincodeProposalPayloadFactory;
use AmericanExpress\HyperledgerFabricClient\ProtoFactory\ChannelHeaderFactory;
use AmericanExpress\HyperledgerFabricClient\ProtoFactory\HeaderFactory;
use AmericanExpress\HyperledgerFabricClient\ProtoFactory\ProposalFactory;
use AmericanExpress\HyperledgerFabricClient\ProtoFactory\SerializedIdentityFactory;
use AmericanExpress\HyperledgerFabricClient\ProtoFactory\SignedProposalFactory;
use AmericanExpress\HyperledgerFabricClient\Transaction\TransactionContext;
use Hyperledger\Fabric\Protos\Peer\SignedProposal;
use PHPUnit\Framework\TestCase;

/**
 * @covers \AmericanExpress\HyperledgerFabricClient\ProtoFactory\SignedProposalFactory
 */
class SignedProposalFactoryTest extends TestCase
{
    public function testFromProposal()
    {
        $channelHeader = ChannelHeaderFactory::create(
            $transactionContext = new TransactionContext(
                SerializedIdentityFactory::fromBytes('Alice', 'Bob'),
                'u58920du89f',
                'MyTransactionId'
            ),
            'MyChannelId',
            'FooBar',
            'FizBuz',
            'v12.34'
        );

        $header = HeaderFactory::fromTransactionContext($transactionContext, $channelHeader);

        $chaincodeProposalPayload = ChaincodeProposalPayloadFactory::fromChaincodeInvocationSpecArgs([
            'foo',
            'bar',
        ]);

        $proposal = ProposalFactory::create($header, $chaincodeProposalPayload);

        $result = SignedProposalFactory::fromProposal($proposal, 'MySignature');

        self::assertInstanceOf(SignedProposal::class, $result);
        self::assertContains('Alice', $result->getProposalBytes());
        self::assertContains('Bob', $result->getProposalBytes());
        self::assertContains('MyChannelId', $result->getProposalBytes());
        self::assertContains('MyTransactionId', $result->getProposalBytes());
        self::assertContains('FooBar', $result->getProposalBytes());
        self::assertContains('FizBuz', $result->getProposalBytes());
        self::assertContains('v12.34', $result->getProposalBytes());
        self::assertContains('u58920du89f', $result->getProposalBytes());
        self::assertContains('foo', $result->getProposalBytes());
        self::assertContains('bar', $result->getProposalBytes());
        self::assertSame('MySignature', $result->getSignature());
    }
}
