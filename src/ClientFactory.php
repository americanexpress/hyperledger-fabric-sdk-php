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

namespace AmericanExpress\HyperledgerFabricClient;

use AmericanExpress\HyperledgerFabricClient\Config\ClientConfigInterface;
use AmericanExpress\HyperledgerFabricClient\EndorserClient\EndorserClientManager;
use AmericanExpress\HyperledgerFabricClient\Header\HeaderGenerator;
use AmericanExpress\HyperledgerFabricClient\Nonce\RandomBytesNonceGenerator;
use AmericanExpress\HyperledgerFabricClient\Peer\PeerFactory;
use AmericanExpress\HyperledgerFabricClient\Signatory\MdanterEccSignatory;
use AmericanExpress\HyperledgerFabricClient\Transaction\TransactionIdentifierGenerator;
use AmericanExpress\HyperledgerFabricClient\User\UserContextFactory;

class ClientFactory
{
    /**
     * @param ClientConfigInterface $config
     * @param string|null $organization
     * @return Client
     */
    public static function fromConfig(
        ClientConfigInterface $config,
        string $organization = null
    ): Client {
        $user = UserContextFactory::fromConfig($config, $organization);

        $signatory = new MdanterEccSignatory($config->getHashAlgorithm());

        $transactionContextFactory = new TransactionIdentifierGenerator(
            new RandomBytesNonceGenerator($config->getNonceSize()),
            $config->getHashAlgorithm()
        );

        return new Client(
            $user,
            $signatory,
            new PeerFactory(new EndorserClientManager()),
            new HeaderGenerator($transactionContextFactory, $config->getEpoch())
        );
    }
}
