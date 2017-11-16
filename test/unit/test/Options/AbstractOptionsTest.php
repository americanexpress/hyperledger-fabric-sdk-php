<?php
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
