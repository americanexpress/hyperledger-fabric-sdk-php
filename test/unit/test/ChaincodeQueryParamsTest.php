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

use AmericanExpress\HyperledgerFabricClient\ChaincodeQueryParams;
use PHPUnit\Framework\TestCase;

/**
 * @covers \AmericanExpress\HyperledgerFabricClient\ChaincodeQueryParams
 */
class ChaincodeQueryParamsTest extends TestCase
{
    /**
     * @var ChaincodeQueryParams
     */
    private $sut;

    protected function setUp()
    {
        $this->sut = new ChaincodeQueryParams();
    }

    public function testChannelId()
    {
        self::assertNull($this->sut->getChannelId());

        $this->sut->setChannelId('MyChannelId');

        self::assertSame('MyChannelId', $this->sut->getChannelId());
    }

    public function testChaincodeName()
    {
        self::assertNull($this->sut->getChaincodeName());

        $this->sut->setChaincodeName('FooBar');

        self::assertSame('FooBar', $this->sut->getChaincodeName());
    }

    public function testChaincodePath()
    {
        self::assertNull($this->sut->getChaincodePath());

        $this->sut->setChaincodePath('FizBuz');

        self::assertSame('FizBuz', $this->sut->getChaincodePath());
    }

    public function testChaincodeVersion()
    {
        self::assertNull($this->sut->getChaincodeVersion());

        $this->sut->setChaincodeVersion('v12.34');

        self::assertSame('v12.34', $this->sut->getChaincodeVersion());
    }

    public function testArgs()
    {
        self::assertCount(0, $this->sut->getArgs());

        $this->sut->setArgs(['foo' => 'bar']);

        self::assertSame(['foo' => 'bar'], $this->sut->getArgs());
    }

    public function testFromArray()
    {
        $sut = new ChaincodeQueryParams([
            'channelId' => 'MyChannelId',
            'chaincodeName' => 'FooBar',
            'chaincodePath' => 'FizBuz',
            'chaincodeVersion' => 'v12.34',
            'args' => [
                'foo' => 'bar',
            ],
        ]);

        self::assertSame('MyChannelId', $sut->getChannelId());
        self::assertSame('FooBar', $sut->getChaincodeName());
        self::assertSame('FizBuz', $sut->getChaincodePath());
        self::assertSame('v12.34', $sut->getChaincodeVersion());
        self::assertSame(['foo' => 'bar'], $sut->getArgs());
    }
}
