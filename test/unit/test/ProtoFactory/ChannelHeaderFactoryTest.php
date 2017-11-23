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
use AmericanExpress\HyperledgerFabricClient\ProtoFactory\SerializedIdentityFactory;
use AmericanExpress\HyperledgerFabricClient\Transaction\TransactionContext;
use Google\Protobuf\Timestamp;
use Hyperledger\Fabric\Protos\Common\ChannelHeader;
use PHPUnit\Framework\TestCase;

/**
 * @covers \AmericanExpress\HyperledgerFabricClient\ProtoFactory\ChannelHeaderFactory
 */
class ChannelHeaderFactoryTest extends TestCase
{
    public function testDefaultCreate()
    {
        $result = ChannelHeaderFactory::create(
            new TransactionContext(
                SerializedIdentityFactory::fromBytes('Alice', 'Bob'),
                'u58920du89f',
                'MyTransactionId'
            ),
            'MyChannelId'
        );

        self::assertInstanceOf(ChannelHeader::class, $result);
        self::assertSame(3, $result->getType());
        self::assertSame(1, $result->getVersion());
        self::assertInstanceOf(Timestamp::class, $result->getTimestamp());
        self::assertSame('MyChannelId', $result->getChannelId());
        self::assertSame('MyTransactionId', $result->getTxId());
        self::assertSame(0, $result->getEpoch());
    }
}
