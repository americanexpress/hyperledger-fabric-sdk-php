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

namespace AmericanExpressTest\HyperledgerFabricClient\Exception;

use AmericanExpress\HyperledgerFabricClient\Exception\ExceptionInterface;
use AmericanExpress\HyperledgerFabricClient\Exception\RuntimeException;
use PHPUnit\Framework\TestCase;

/**
 * @covers \AmericanExpress\HyperledgerFabricClient\Exception\RuntimeException
 */
class RuntimeExceptionTest extends TestCase
{
    public function testFromException()
    {
        $exception = new \RuntimeException('FooBar', 1234, new \Exception());

        $result = RuntimeException::fromException($exception);

        self::assertInstanceOf(RuntimeException::class, $result);
        self::assertInstanceOf(ExceptionInterface::class, $result);
        self::assertSame('FooBar', $result->getMessage());
        self::assertSame(1234, $result->getCode());
        self::assertSame($exception, $result->getPrevious());
    }
}
