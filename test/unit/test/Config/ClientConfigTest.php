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
use AmericanExpress\HyperledgerFabricClient\Organization\OrganizationOptionsInterface;
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
        self::assertNull($this->sut->getIn(['Alice', 'Bob']));
        self::assertSame('FizBuz', $this->sut->getIn(['Alice', 'Bob'], 'FizBuz'));
    }

    public function testGetDefaults()
    {
        $sut = new ClientConfig([]);

        self::assertSame(5000, $sut->getIn(['timeout']));
        self::assertSame(0, $sut->getIn(['epoch']));
        self::assertSame('sha256', (string)$sut->getIn(['crypto-hash-algo']));
        self::assertSame(24, $sut->getIn(['nonce-size']));
    }

    public function testOverrideDefaults()
    {
        $sut = new ClientConfig([
            'timeout' => 10,
            'epoch' => -100,
            'crypto-hash-algo' => 'md5',
            'nonce-size' => 3,
        ]);

        self::assertSame(10, $sut->getIn(['timeout']));
        self::assertSame(-100, $sut->getIn(['epoch']));
        self::assertSame('md5', (string)$sut->getIn(['crypto-hash-algo']));
        self::assertSame(3, $sut->getIn(['nonce-size']));
    }

    public function testNonceSizeAssessor()
    {
        $sut = new ClientConfig([
            'nonce-size' => 234,
        ]);

        self::assertSame(234, $sut->getNonceSize());
    }

    public function testNonceSizeAssessorDefaultValue()
    {
        $sut = new ClientConfig([]);

        self::assertSame(24, $sut->getNonceSize());
    }

    public function testEpochAssessor()
    {
        $sut = new ClientConfig([
            'epoch' => 234,
        ]);

        self::assertSame(234, $sut->getEpoch());
    }

    public function testEpochAssessorDefaultValue()
    {
        $sut = new ClientConfig([]);

        self::assertSame(0, $sut->getEpoch());
    }

    public function testHashAlgorithmAssessor()
    {
        $sut = new ClientConfig([
            'crypto-hash-algo' => 'whirlpool',
        ]);

        self::assertSame('whirlpool', (string)$sut->getHashAlgorithm());
    }

    public function testHashAlgorithmAssessorDefaultValue()
    {
        $sut = new ClientConfig([]);

        self::assertSame('sha256', (string)$sut->getHashAlgorithm());
    }

    public function testTimeoutAssessor()
    {
        $sut = new ClientConfig([
            'timeout' => 234,
        ]);

        self::assertSame(234, $sut->getTimeout());
    }

    public function testTimeoutAssessorDefaultValue()
    {
        $sut = new ClientConfig([]);

        self::assertSame(5000, $sut->getTimeout());
    }

    public function testGetOrganizationOptionsByNetworkAndOrganizationNames()
    {
        $sut = new ClientConfig([
            'organizations' => [
                [
                    'name' => 'peerOrg1',
                    'mspid' => 'Org1MSP',
                    'admin_certs' => __FILE__,
                    'private_key' => __FILE__,
                ],
            ],
        ]);

        self::assertInstanceOf(
            OrganizationOptionsInterface::class,
            $sut->getOrganizationByName('peerOrg1')
        );
    }

    public function testGetOrganizationOptionsByInvalidNetworkAndOrganizationNames()
    {
        $sut = new ClientConfig([]);

        self::assertNull($sut->getOrganizationByName('FooBar'));
    }

    /**
     * @expectedException \AmericanExpress\HyperledgerFabricClient\Exception\InvalidArgumentException
     */
    public function testThrowsExceptionOnInvalidHashAlgoInConfigFile()
    {
        $config = new ClientConfig([
            'crypto-hash-algo' => 'DEFINITELY INVALID'
        ]);

        $config->getHashAlgorithm();
    }

    public function testGetDefaultOrganization()
    {
        $config = new ClientConfig([
            'organizations' => [
                [
                    'name' => 'peerOrg1',
                ],
            ],
        ]);

        $result = $config->getDefaultOrganization();

        self::assertInstanceOf(OrganizationOptionsInterface::class, $result);
        self::assertSame('peerOrg1', $result->getName());
    }
}
