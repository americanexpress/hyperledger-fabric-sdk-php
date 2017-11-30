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
use AmericanExpress\HyperledgerFabricClient\ProtoFactory\ChannelHeaderFactory;
use AmericanExpress\HyperledgerFabricClient\ProtoFactory\HeaderFactory;
use AmericanExpress\HyperledgerFabricClient\ProtoFactory\ProposalFactory;
use AmericanExpress\HyperledgerFabricClient\ProtoFactory\SerializedIdentityFactory;
use AmericanExpress\HyperledgerFabricClient\ProtoFactory\SignatureHeaderFactory;
use Hyperledger\Fabric\Protos\Peer\Proposal;
use PHPUnit\Framework\TestCase;

/**
 * @covers \AmericanExpress\HyperledgerFabricClient\ProtoFactory\ProposalFactory
 */
class ProposalFactoryTest extends TestCase
{
    public function testCreate()
    {
        $serializedIdentity = SerializedIdentityFactory::fromBytes('Alice', 'Bob');
        $nonce = 'u58920du89f';
        $txId = 'MyTransactionId';

        $channelHeader = ChannelHeaderFactory::create('MyChannelId');
        $channelHeader->setTxId($txId);

        $header = HeaderFactory::create(SignatureHeaderFactory::create(
            $serializedIdentity,
            $nonce
        ), $channelHeader);

        $chaincodeInvocationSpec = ChaincodeInvocationSpecFactory::fromArgs([
            'foo',
            'bar',
        ]);

        $chaincodeProposalPayload = ChaincodeProposalPayloadFactory::fromChaincodeInvocationSpec(
            $chaincodeInvocationSpec
        );

        $result = ProposalFactory::create($header, $chaincodeProposalPayload->serializeToString());
        self::assertInstanceOf(Proposal::class, $result);
        self::assertContains('Alice', $result->getHeader());
        self::assertContains('Bob', $result->getHeader());
        self::assertContains('MyChannelId', $result->getHeader());
        self::assertContains('MyTransactionId', $result->getHeader());
        self::assertContains('u58920du89f', $result->getHeader());
        self::assertContains('foo', $result->getPayload());
        self::assertContains('bar', $result->getPayload());
        self::assertSame('', $result->getExtension());
    }
}
