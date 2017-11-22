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

namespace AmericanExpressTest\HyperledgerFabricClient\Channel;

use AmericanExpress\HyperledgerFabricClient\Channel\ChannelManager;
use AmericanExpress\HyperledgerFabricClient\ChannelInterface;
use AmericanExpress\HyperledgerFabricClient\Config\ClientConfig;
use PHPUnit\Framework\TestCase;

/**
 * @covers \AmericanExpress\HyperledgerFabricClient\Channel\ChannelManager
 */
class ChannelManagerTest extends TestCase
{
    /**
     * @var ChannelManager
     */
    private $sut;

    protected function setUp()
    {
        $this->sut = new ChannelManager(new ClientConfig([]));
    }

    public function testGet()
    {
        $endorserClient = $this->sut->get('FooBar');

        self::assertInstanceOf(ChannelInterface::class, $endorserClient);
        self::assertSame($endorserClient, $this->sut->get('FooBar'));
    }
}
