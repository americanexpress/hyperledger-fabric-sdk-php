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

use AmericanExpress\HyperledgerFabricClient\ProtoFactory\ChannelHeaderFactory;
use AmericanExpress\HyperledgerFabricClient\ProtoFactory\HeaderFactory;
use AmericanExpress\HyperledgerFabricClient\ProtoFactory\SerializedIdentityFactory;
use AmericanExpress\HyperledgerFabricClient\ProtoFactory\SignatureHeaderFactory;
use AmericanExpress\HyperledgerFabricClient\Transaction\TransactionContext;
use Hyperledger\Fabric\Protos\Common\Header;
use PHPUnit\Framework\TestCase;

/**
 * @covers \AmericanExpress\HyperledgerFabricClient\ProtoFactory\HeaderFactory
 */
class HeaderFactoryTest extends TestCase
{
    public function testCreate()
    {
        $channelHeader = ChannelHeaderFactory::create(
            new TransactionContext(
                $serializedIdentity = SerializedIdentityFactory::fromBytes('Alice', 'Bob'),
                $nonce = 'u58920du89f',
                'MyTransactionId'
            ),
            'MyChannelId'
        );

        $signatureHeader = SignatureHeaderFactory::create($serializedIdentity, $nonce);

        $result = HeaderFactory::create($channelHeader, $signatureHeader);

        self::assertInstanceOf(Header::class, $result);
        self::assertContains('MyChannelId', $result->getChannelHeader());
        self::assertContains('MyTransactionId', $result->getChannelHeader());
        self::assertContains('Alice', $result->getSignatureHeader());
        self::assertContains('Bob', $result->getSignatureHeader());
        self::assertContains('u58920du89f', $result->getSignatureHeader());
    }

    public function testCreateFromSerializedIdentity()
    {
        $channelHeader = ChannelHeaderFactory::create(
            new TransactionContext(
                $serializedIdentity = SerializedIdentityFactory::fromBytes('Alice', 'Bob'),
                $nonce = 'u58920du89f',
                'MyTransactionId'
            ),
            'MyChannelId'
        );

        $result = HeaderFactory::createFromSerializedIdentity($channelHeader, $serializedIdentity, $nonce);

        self::assertInstanceOf(Header::class, $result);
        self::assertContains('MyChannelId', $result->getChannelHeader());
        self::assertContains('MyTransactionId', $result->getChannelHeader());
        self::assertContains('Alice', $result->getSignatureHeader());
        self::assertContains('Bob', $result->getSignatureHeader());
        self::assertContains('u58920du89f', $result->getSignatureHeader());
    }

    public function testFromTransactionContext()
    {
        $channelHeader = ChannelHeaderFactory::create(
            $transactionContext = new TransactionContext(
                SerializedIdentityFactory::fromBytes('Alice', 'Bob'),
                'u58920du89f',
                'MyTransactionId'
            ),
            'MyChannelId'
        );

        $result = HeaderFactory::fromTransactionContext($channelHeader, $transactionContext);

        self::assertInstanceOf(Header::class, $result);
        self::assertContains('MyChannelId', $result->getChannelHeader());
        self::assertContains('MyTransactionId', $result->getChannelHeader());
        self::assertContains('Alice', $result->getSignatureHeader());
        self::assertContains('Bob', $result->getSignatureHeader());
        self::assertContains('u58920du89f', $result->getSignatureHeader());
    }
}
