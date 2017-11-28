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

namespace AmericanExpressTest\HyperledgerFabricClient\Transaction;

use AmericanExpress\HyperledgerFabricClient\Transaction\TransactionIdentifier;
use PHPUnit\Framework\TestCase;

/**
 * @covers \AmericanExpress\HyperledgerFabricClient\Transaction\TransactionIdentifier
 */
class TransactionIdentifierTest extends TestCase
{
    public function testValues()
    {
        $nonce = 'u4i6o2j6n6';
        $txId = 'i3o6kf8t0ek';

        $sut = new TransactionIdentifier($txId, $nonce);

        self::assertSame($nonce, $sut->getNonce());
        self::assertSame($txId, $sut->getId());
    }
}
