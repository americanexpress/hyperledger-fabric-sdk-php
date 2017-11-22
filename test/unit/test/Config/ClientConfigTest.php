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

namespace AmericanExpressTest\HyperledgerFabricClient\Config;

use AmericanExpress\HyperledgerFabricClient\Config\ClientConfig;
use org\bovigo\vfs\vfsStream;
use org\bovigo\vfs\vfsStreamDirectory;
use PHPUnit\Framework\TestCase;

/**
 * @covers \AmericanExpress\HyperledgerFabricClient\Config\ClientConfig
 */
class ClientConfigTest extends TestCase
{
    /**
     * @var vfsStreamDirectory
     */
    private $files;

    /**
     * @var ClientConfig
     */
    private $sut;

    protected function setUp()
    {
        $this->files = vfsStream::setup('test');

        $this->sut = new ClientConfig([
            'foo' => [
                'bar' => 'FizBuz',
            ],
        ]);
    }

    public function testGetIn()
    {
        self::assertSame('FizBuz', $this->sut->getIn(['foo', 'bar']));
        self::assertSame(['bar' => 'FizBuz'], $this->sut->getIn(['foo']));
        self::assertSame(null, $this->sut->getIn(['Alice', 'Bob']));
        self::assertSame('FizBuz', $this->sut->getIn(['Alice', 'Bob'], 'FizBuz'));
    }

    public function testGetDefaults()
    {
        $sut = new ClientConfig([]);

        self::assertSame(5000, $sut->getIn(['timeout']));
        self::assertSame(0, $sut->getIn(['epoch']));
        self::assertSame('sha256', $sut->getIn(['crypto-hash-algo']));
        self::assertSame(24, $sut->getIn(['nonce-size']));
    }

    public function testOverrideDefaults()
    {
        $sut = new ClientConfig([
            'timeout' => 10,
            'epoch' => -100,
            'crypto-hash-algo' => 'md5',
            'nonce-size'  => 3,
        ]);

        self::assertSame(10, $sut->getIn(['timeout']));
        self::assertSame(-100, $sut->getIn(['epoch']));
        self::assertSame('md5', $sut->getIn(['crypto-hash-algo']));
        self::assertSame(3, $sut->getIn(['nonce-size']));
    }
}
