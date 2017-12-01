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

use AmericanExpress\HyperledgerFabricClient\Client;
use AmericanExpress\HyperledgerFabricClient\ClientFactory;
use AmericanExpress\HyperledgerFabricClient\Config\ClientConfig;
use PHPUnit\Framework\TestCase;

/**
 * @covers \AmericanExpress\HyperledgerFabricClient\ClientFactory
 */
class ClientFactoryTest extends TestCase
{
    public function testFromConfig()
    {
        $config = new ClientConfig([
            'organizations' => [
                [
                    'name' => 'peerOrg1',
                    'mspId' => 'FooBar',
                    'adminCerts' => __FILE__,
                ],
            ],
        ]);

        $client = ClientFactory::fromConfig($config, 'peerOrg1');

        self::assertInstanceOf(Client::class, $client);
    }

    /**
     * @expectedException \AmericanExpress\HyperledgerFabricClient\Exception\InvalidArgumentException
     */
    public function testFromInvalidConfig()
    {
        $config = new ClientConfig([]);

        ClientFactory::fromConfig($config, 'peerOrg1');
    }

    /**
     * @expectedException \AmericanExpress\HyperledgerFabricClient\Exception\InvalidArgumentException
     */
    public function testThrowsExceptionOnInvalidOrganization()
    {
        $config = new ClientConfig([
            'organizations' => [
                [
                    'name' => 'peerOrg1',
                    'mspId' => 'FooBar',
                    'adminCerts' => __FILE__,
                ],
            ],
        ]);

        ClientFactory::fromConfig($config,'INVALID-ORG');
    }

    /**
     * @expectedException \AmericanExpress\HyperledgerFabricClient\Exception\InvalidArgumentException
     */
    public function testThrowsExceptionOnInvalidNonce()
    {
        $config = new ClientConfig([
            'organizations' => [
                [
                    'name' => 'peerOrg1',
                    'mspId' => 'FooBar',
                    'adminCerts' => __FILE__,
                ],
            ],
            'nonce-size' => -5
        ]);

        ClientFactory::fromConfig($config);
    }
}
