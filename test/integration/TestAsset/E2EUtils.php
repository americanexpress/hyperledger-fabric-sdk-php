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

namespace AmericanExpressTest\Integration\TestAsset;

use AmericanExpress\HyperledgerFabricClient\Client\ClientFactory;
use AmericanExpress\HyperledgerFabricClient\Config\ClientConfigFactory;
use AmericanExpress\HyperledgerFabricClient\Transaction\TransactionRequest;
use Hyperledger\Fabric\Protos\Peer\ChaincodeID;

class E2EUtils
{
    /**
     * @param string $org
     * @return string
     */
    public function queryChaincode(string $org): string
    {
        $config = ClientConfigFactory::fromFile(new \SplFileObject(__DIR__ . '/../config.php'));
        $request = new TransactionRequest([
            'organization' => $config->getOrganization('test-network', $org),
            'peer' => 'peer1'
        ]);

        $fabricProposal = ClientFactory::fromConfig($config)
            ->getChannel('foo')
            ->queryByChainCode(
                $request,
                (new ChaincodeID())
                    ->setPath('github.com/example_cc')
                    ->setName('example_cc')
                    ->setVersion('1'),
                [
                    'invoke',
                    'query',
                    'a',
                ]
            );

        return $fabricProposal->getPayload();
    }
}
