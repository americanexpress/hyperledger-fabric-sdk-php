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

namespace AmericanExpressIntegrationTest\HyperledgerFabricClient\Chaincode;

use AmericanExpress\HyperledgerFabricClient\Channel\ChannelProviderInterface;
use AmericanExpress\HyperledgerFabricClient\ClientFactory;
use AmericanExpress\HyperledgerFabricClient\Config\ClientConfigFactory;
use AmericanExpress\HyperledgerFabricClient\Config\ClientConfigInterface;
use AmericanExpress\HyperledgerFabricClient\Peer\PeerFactory;
use AmericanExpress\HyperledgerFabricClient\Transaction\TransactionOptions;
use PHPUnit\Framework\TestCase;

class ChaincodeTest extends TestCase
{
    /**
     * @var ClientConfigInterface
     */
    private static $config;

    /**
     * @var ChannelProviderInterface
     */
    private $channelProvider;

    public static function setUpBeforeClass()
    {
        self::$config = ClientConfigFactory::fromFile(
            new \SplFileObject(__DIR__ . '/../../config.php')
        );
    }

    protected function setUp()
    {
        $this->channelProvider = ClientFactory::fromConfig(self::$config);
    }

    /**
     * Demonstrates the most basic usage, where the client has only one peer per org,
     * and selects the first org by default.
     */
    public function testChainCodeEntityQuery()
    {
        $responses = $this->channelProvider->getChannel('foo')
            ->getChaincode('example_cc')
            ->invoke('query', 'a');

        self::assertCount(1, $responses->getProposalResponses());
        self::assertCount(0, $responses->getExceptions());
    }

    /**
     * Demonstrates extended usage of the client, where an org is explicitly specified per client
     * and the peer is explicitly specified at query-time via overridable transaction-options.
     */
    public function testQueryChaincodeWithCustomPeer()
    {
        $peer = (new PeerFactory())->fromArray([
            'requests' => 'localhost:7051',
        ]);

        $options = new TransactionOptions([
            'peers' => [$peer],
        ]);

        $responses = ClientFactory::fromConfig(self::$config, 'peerOrg1')
            ->getChannel('foo')
            ->getChaincode([
                'name' => 'example_cc',
                'version' => '1',
                'path' => 'github.com/example_cc'
            ])
            ->invoke('query', 'a', $options);

        self::assertCount(1, $responses->getProposalResponses());
        self::assertCount(0, $responses->getExceptions());
    }
}
