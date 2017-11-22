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

namespace AmericanExpressTest\HyperledgerFabricClient\Options;

use AmericanExpressTest\HyperledgerFabricClient\TestAsset\FakeOptions;
use PHPUnit\Framework\TestCase;

/**
 * @covers \AmericanExpress\HyperledgerFabricClient\Options\AbstractOptions
 */
class AbstractOptionsTest extends TestCase
{
    public function testDefault()
    {
        $sut = new FakeOptions();

        self::assertNull($sut->getFooBar());
    }

    public function testSetOptionFromArrayConstructorArg()
    {
        $sut = new FakeOptions([
            'FooBar' => 'FizBuz',
        ]);

        self::assertSame('FizBuz', $sut->getFooBar());
    }

    public function testSetOptionFromIteratorConstructorArg()
    {
        $sut = new FakeOptions(new \ArrayIterator([
            'FooBar' => 'FizBuz',
        ]));

        self::assertSame('FizBuz', $sut->getFooBar());
    }

    public function testSetOptionIsCaseInsensitive()
    {
        $sut = new FakeOptions([
            'foobar' => 'FizBuz',
        ]);

        self::assertSame('FizBuz', $sut->getFooBar());
    }

    public function testSetOptionStripsUnderscores()
    {
        $sut = new FakeOptions([
            '_Foo_Bar_' => 'FizBuz',
        ]);

        self::assertSame('FizBuz', $sut->getFooBar());
    }

    /**
     * @expectedException \AmericanExpress\HyperledgerFabricClient\Exception\BadMethodCallException
     * @expectedExceptionMessage FakeOptions::setAlice is not callable.
     */
    public function testSetInvalidOption()
    {
        new FakeOptions([
            'Alice' => 'Bob',
        ]);
    }
}
