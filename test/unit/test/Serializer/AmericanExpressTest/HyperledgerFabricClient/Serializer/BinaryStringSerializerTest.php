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

namespace AmericanExpressTest\HyperledgerFabricClient\Serializer;

use AmericanExpress\HyperledgerFabricClient\Serializer\BinaryStringSerializer;
use PHPUnit\Framework\TestCase;

/**
 * @covers \AmericanExpress\HyperledgerFabricClient\Serializer\BinaryStringSerializer
 */
class BinaryStringSerializerTest extends TestCase
{
    /**
     * @var BinaryStringSerializer
     */
    private $sut;

    protected function setUp()
    {
        $this->sut = new BinaryStringSerializer();
    }

    public function testSerialize()
    {
        self::assertSame('AAA=', base64_encode($this->sut->serialize([
            'foo',
            'bar',
        ])));
    }

    public function testDeserialize()
    {
        self::assertSame([1 => 0, 2 => 0], $this->sut->deserialize(base64_decode('AAA=')));
    }
}
