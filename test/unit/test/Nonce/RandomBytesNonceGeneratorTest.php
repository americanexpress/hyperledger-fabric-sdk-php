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

namespace AmericanExpressTest\HyperledgerFabricClient\Nonce;

use AmericanExpress\HyperledgerFabricClient\Nonce\RandomBytesNonceGenerator;
use PHPUnit\Framework\TestCase;

/**
 * @covers \AmericanExpress\HyperledgerFabricClient\Nonce\RandomBytesNonceGenerator
 */
class RandomBytesNonceGeneratorTest extends TestCase
{
    /**
     * @var RandomBytesNonceGenerator
     */
    private $sut;

    protected function setUp()
    {
        $this->sut = new RandomBytesNonceGenerator();
    }

    public function testDefaultNonceLength()
    {
        $nonce = $this->sut->generateNonce();

        self::assertSame(24, strlen($nonce));
    }

    public function testConfigurableNonceLength()
    {
        $nonce = (new RandomBytesNonceGenerator(3))->generateNonce();

        self::assertSame(3, strlen($nonce));
    }

    /**
     * @expectedException \AmericanExpress\HyperledgerFabricClient\Exception\InvalidArgumentException
     */
    public function testInvalidNonceSize()
    {
        new RandomBytesNonceGenerator(-1);
    }
}
