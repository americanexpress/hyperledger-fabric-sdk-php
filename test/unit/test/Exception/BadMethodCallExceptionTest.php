<?php
declare(strict_types=1);

namespace AmericanExpressTest\HyperledgerFabricClient\Exception;

use AmericanExpress\HyperledgerFabricClient\Exception\ExceptionInterface;
use AmericanExpress\HyperledgerFabricClient\Exception\BadMethodCallException;
use PHPUnit\Framework\TestCase;

/**
 * @covers \AmericanExpress\HyperledgerFabricClient\Exception\BadMethodCallException
 */
class BadMethodCallExceptionTest extends TestCase
{
    public function testFromException()
    {
        $exception = new \BadMethodCallException('FooBar', 1234, new \Exception());

        $result = BadMethodCallException::fromException($exception);

        self::assertInstanceOf(BadMethodCallException::class, $result);
        self::assertInstanceOf(ExceptionInterface::class, $result);
        self::assertSame('FooBar', $result->getMessage());
        self::assertSame(1234, $result->getCode());
        self::assertSame($exception, $result->getPrevious());
    }
}
