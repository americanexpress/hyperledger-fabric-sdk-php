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

namespace AmericanExpressTest\HyperledgerFabricClient;

use AmericanExpress\HyperledgerFabricClient\ChannelContext;
use PHPUnit\Framework\TestCase;

/**
 * @covers \AmericanExpress\HyperledgerFabricClient\ChannelContext
 */
class ChannelContextTest extends TestCase
{
    /**
     * @var ChannelContext
     */
    private $sut;

    protected function setUp()
    {
        $this->sut = new ChannelContext();
    }

    public function testHost()
    {
        self::assertNull($this->sut->getHost());

        $this->sut->setHost('example.com');

        self::assertSame('example.com', $this->sut->getHost());
    }

    public function testMspId()
    {
        self::assertNull($this->sut->getMspId());

        $this->sut->setMspId('1234');

        self::assertSame('1234', $this->sut->getMspId());
    }

    public function testAdminCerts()
    {
        self::assertNull($this->sut->getAdminCerts());

        $file = new \SplFileObject(__FILE__);

        $this->sut->setAdminCerts($file);

        self::assertSame($file, $this->sut->getAdminCerts());
    }

    public function testPrivateKey()
    {
        self::assertNull($this->sut->getPrivateKey());

        $file = new \SplFileObject(__FILE__);

        $this->sut->setPrivateKey($file);

        self::assertSame($file, $this->sut->getPrivateKey());
    }

    public function testFromArray()
    {
        $file = new \SplFileObject(__FILE__);

        $sut = new ChannelContext([
            'host' => 'example.com',
            'mspId' => '1234',
            'adminCerts' => $file,
            'privateKey' => $file,
        ]);

        self::assertSame('example.com', $sut->getHost());
        self::assertSame('1234', $sut->getMspId());
        self::assertSame($file, $sut->getAdminCerts());
        self::assertSame($file, $sut->getPrivateKey());
    }
}
