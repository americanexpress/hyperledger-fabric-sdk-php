<?php
declare(strict_types=1);

namespace AmericanExpressTest\HyperledgerFabricClient\ValueObject;

use AmericanExpress\HyperledgerFabricClient\ValueObject\StringValue;
use PHPUnit\Framework\TestCase;

/**
 * @covers \AmericanExpress\HyperledgerFabricClient\ValueObject\StringValue
 */
class StringValueTest extends TestCase
{
    public function testStringValue()
    {
        $sut = new StringValue('FooBar');

        self::assertSame('FooBar', (string) $sut);
    }
}
