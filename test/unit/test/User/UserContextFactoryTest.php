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

namespace AmericanExpressTest\HyperledgerFabricClient\User;

use AmericanExpress\HyperledgerFabricClient\Config\ClientConfig;
use AmericanExpress\HyperledgerFabricClient\Organization\OrganizationOptionsInterface;
use AmericanExpress\HyperledgerFabricClient\User\UserContext;
use AmericanExpress\HyperledgerFabricClient\User\UserContextFactory;
use Hyperledger\Fabric\Protos\MSP\SerializedIdentity;
use PHPUnit\Framework\TestCase;

/**
 * @covers \AmericanExpress\HyperledgerFabricClient\User\UserContextFactory
 */
class UserContextFactoryTest extends TestCase
{
    public function testFromConfig()
    {
        $config = new ClientConfig([
            'organizations' => [
                [
                    'name' => 'peerOrg1',
                    'mspid' => 'Org1MSP',
                    'adminCerts' => __FILE__,
                ],
            ],
        ]);

        $result = UserContextFactory::fromConfig($config, 'peerOrg1');

        self::assertInstanceOf(UserContext::class, $result);
        self::assertInstanceOf(SerializedIdentity::class, $result->getIdentity());
        self::assertInstanceOf(OrganizationOptionsInterface::class, $result->getOrganization());
        self::assertSame('peerOrg1', $result->getOrganization()->getName());
        self::assertSame('Org1MSP', $result->getOrganization()->getMspId());
        self::assertSame(__FILE__, $result->getOrganization()->getAdminCerts());
    }

    public function testFromConfigWithDefaultOrganization()
    {
        $config = new ClientConfig([
            'organizations' => [
                [
                    'name' => 'peerOrg1',
                    'mspid' => 'Org1MSP',
                    'adminCerts' => __FILE__,
                ],
            ],
        ]);

        $result = UserContextFactory::fromConfig($config);

        self::assertInstanceOf(UserContext::class, $result);
        self::assertInstanceOf(SerializedIdentity::class, $result->getIdentity());
        self::assertInstanceOf(OrganizationOptionsInterface::class, $result->getOrganization());
        self::assertSame('peerOrg1', $result->getOrganization()->getName());
        self::assertSame('Org1MSP', $result->getOrganization()->getMspId());
        self::assertSame(__FILE__, $result->getOrganization()->getAdminCerts());
    }

    public function testFromConfigWithMultipleOrganizations()
    {
        $config = new ClientConfig([
            'organizations' => [
                [
                    'name' => 'peerOrg1',
                    'mspid' => 'Org1MSP',
                    'adminCerts' => __FILE__,
                ],
                [
                    'name' => 'peerOrg2',
                    'mspid' => 'Org2MSP',
                    'adminCerts' => __FILE__,
                ],
            ],
        ]);

        $result = UserContextFactory::fromConfig($config, 'peerOrg2');

        self::assertInstanceOf(UserContext::class, $result);
        self::assertInstanceOf(SerializedIdentity::class, $result->getIdentity());
        self::assertInstanceOf(OrganizationOptionsInterface::class, $result->getOrganization());
        self::assertSame('peerOrg2', $result->getOrganization()->getName());
        self::assertSame('Org2MSP', $result->getOrganization()->getMspId());
        self::assertSame(__FILE__, $result->getOrganization()->getAdminCerts());
    }

    /**
     * @expectedException \AmericanExpress\HyperledgerFabricClient\Exception\UnexpectedValueException
     */
    public function testFromConfigWithInvalidOrganization()
    {
        $config = new ClientConfig([
            'organizations' => [
                [
                    'name' => 'peerOrg1',
                    'mspid' => 'Org1MSP',
                    'adminCerts' => __FILE__,
                ],
            ],
        ]);

        UserContextFactory::fromConfig($config, 'FooBar');
    }
}
