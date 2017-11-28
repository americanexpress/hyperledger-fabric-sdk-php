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

namespace AmericanExpressTest\Integration\Test;

use AmericanExpress\HyperledgerFabricClient\ClientFactory;
use AmericanExpress\HyperledgerFabricClient\Config\ClientConfigFactory;
use AmericanExpress\HyperledgerFabricClient\Peer\PeerOptions;
use AmericanExpress\HyperledgerFabricClient\Transaction\TransactionOptions;
use AmericanExpressTest\Integration\TestAsset\E2EUtils;
use PHPUnit\Framework\TestCase;

class QueryTest extends TestCase
{
    public function testQueryChainCode()
    {
        $e2e = new E2EUtils();
        $result = $e2e->queryChaincode('peerOrg1');
        $this->assertNotNull($result);
    }

    public function testChainCodeEntityQuery()
    {
        $config = ClientConfigFactory::fromFile(new \SplFileObject(__DIR__ . '/../config.php'));

        $fabricProposal = ClientFactory::fromConfig($config, 'peerOrg1')
            ->getChannel('foo')
            ->getChaincode(['name' => 'example_cc', 'version' => '1', 'path' => 'github.com/example_cc'])
            ->invoke('query', 'a');

        $payload = $fabricProposal->getPayload();
        $this->assertNotNull($payload);
    }

    public function testQueryChaincodeWithCustomPeer()
    {
        $config = ClientConfigFactory::fromFile(new \SplFileObject(__DIR__ . '/../config.php'));

        $request = new TransactionOptions([
            'peer' => new PeerOptions([
                'requests' => 'localhost:7051',
            ]),
        ]);

        $fabricProposal = ClientFactory::fromConfig($config, 'peerOrg1')
            ->getChannel('foo')
            ->getChaincode(['name' => 'example_cc', 'version' => '1', 'path' => 'github.com/example_cc'])
            ->invoke('query', 'a', $request);

        $payload = $fabricProposal->getPayload();
        $this->assertNotNull($payload);
    }
}
