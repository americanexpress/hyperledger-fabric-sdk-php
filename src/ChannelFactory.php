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

use AmericanExpress\HyperledgerFabricClient\Client\ClientFactory;
use AmericanExpress\HyperledgerFabricClient\Config\ClientConfigInterface;
use AmericanExpress\HyperledgerFabricClient\Exception\InvalidArgumentException;
use AmericanExpress\HyperledgerFabricClient\Nonce\RandomBytesNonceGenerator;
use AmericanExpress\HyperledgerFabricClient\Transaction\TransactionContextFactory;
use AmericanExpress\HyperledgerFabricClient\Transaction\TransactionIdGenerator;

class ChannelFactory
{
    /**
     * @param string $name
     * @param ClientConfigInterface $config
     * @return Channel
     * @throws \AmericanExpress\HyperledgerFabricClient\Exception\RuntimeException
     * @throws \AmericanExpress\HyperledgerFabricClient\Exception\InvalidArgumentException
     */
    public static function fromConfig(string $name, ClientConfigInterface $config): Channel
    {
        try {
            $hashAlgo = new HashAlgorithm($config->getHashAlgorithm());
        } catch (InvalidArgumentException $e) {
            throw new InvalidArgumentException(
                "Unable to create Channel from Config; Invalid 'crypto-hash-algo' supplied",
                $e->getCode(),
                $e
            );
        }

        $transactionContextFactory = new TransactionContextFactory(
            new RandomBytesNonceGenerator($config->getNonceSize()),
            new TransactionIdGenerator($hashAlgo)
        );

        $client = ClientFactory::fromConfig($config);

        return new Channel($name, $client, $transactionContextFactory);
    }
}
