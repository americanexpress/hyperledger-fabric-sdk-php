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

use AmericanExpress\HyperledgerFabricClient\ProtoFactory\TimestampFactory;
use Google\Protobuf\Timestamp;
use PHPUnit\Framework\TestCase;

/**
 * @covers \AmericanExpress\HyperledgerFabricClient\ProtoFactory\TimestampFactory
 */
class TimestampFactoryTest extends TestCase
{
    public function testBuildCurrentTimestamp()
    {
        $timestamp = TimestampFactory::fromDateTime();

        self::assertInstanceOf(Timestamp::class, $timestamp);
        self::assertNotNull($timestamp->getSeconds());
        self::assertNotNull($timestamp->getNanos());
    }

    /**
     * @param \DateTime $dateTime
     * @param int $seconds
     * @param int $nanos
     * @dataProvider dataBuildTimestamp
     */
    public function testBuildTimestamp(\DateTime $dateTime, int $seconds, int $nanos)
    {
        $timestamp = TimestampFactory::fromDateTime($dateTime);

        self::assertInstanceOf(Timestamp::class, $timestamp);
        self::assertSame($seconds, $timestamp->getSeconds());
        self::assertSame($nanos, $timestamp->getNanos());
    }

    public function dataBuildTimestamp(): array
    {
        return [
            // Now-ish.
            [new \DateTime('2017-11-14T11:23:45', timezone_open('UTC')), 1510658625, 0],
            [new \DateTime('2017-11-14T11:23:45.6789', timezone_open('UTC')), 1510658625, 678900000],

            // Epoch
            [new \DateTime('1970-01-01T00:00:00', timezone_open('UTC')), 0, 0],
            [new \DateTime('1970-01-01T00:00:00.1234', timezone_open('UTC')), 0, 123400000],

            // Epoch -1

            [new \DateTime('1969-12-31T23:59:59', timezone_open('UTC')), -1, 0],
            [new \DateTime('1969-12-31T23:59:59.1234', timezone_open('UTC')), -1, 123400000],

            // Epoch +1
            [new \DateTime('1970-01-01T00:00:01', timezone_open('UTC')), 1, 0],
            [new \DateTime('1970-01-01T00:00:01.1234', timezone_open('UTC')), 1, 123400000],

            // Proto's lower limit
            [new \DateTime('0001-01-01T00:00:00', timezone_open('UTC')), -62135596800, 0],

            // Proto's upper limit
            [new \DateTime('9999-12-31T23:59:59', timezone_open('UTC')), 253402300799, 0],
        ];
    }
}
