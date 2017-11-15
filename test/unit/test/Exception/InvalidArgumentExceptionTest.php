<?php
declare(strict_types=1);

namespace AmericanExpressTest\HyperledgerFabricClient\Exception;

use AmericanExpress\HyperledgerFabricClient\Exception\ExceptionInterface;
use AmericanExpress\HyperledgerFabricClient\Exception\InvalidArgumentException;
use PHPUnit\Framework\TestCase;

/**
 * @covers \AmericanExpress\HyperledgerFabricClient\Exception\InvalidArgumentException
 */
class InvalidArgumentExceptionTest extends TestCase
{
    public function testFromException()
    {
        $exception = new \InvalidArgumentException('FooBar', 1234, new \Exception());

        $result = InvalidArgumentException::fromException($exception);

        self::assertInstanceOf(InvalidArgumentException::class, $result);
        self::assertInstanceOf(ExceptionInterface::class, $result);
        self::assertSame('FooBar', $result->getMessage());
        self::assertSame(1234, $result->getCode());
        self::assertSame($exception, $result->getPrevious());
    }
}
