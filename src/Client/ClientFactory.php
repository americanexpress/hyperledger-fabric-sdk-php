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

namespace AmericanExpress\HyperledgerFabricClient\Client;

use AmericanExpress\HyperledgerFabricClient\Config\ClientConfigInterface;
use AmericanExpress\HyperledgerFabricClient\EndorserClientManager;
use AmericanExpress\HyperledgerFabricClient\Exception\UnexpectedValueException;
use AmericanExpress\HyperledgerFabricClient\ProtoFactory\SerializedIdentityFactory;
use AmericanExpress\HyperledgerFabricClient\Signatory\MdanterEccSignatory;

class ClientFactory
{
    /**
     * @param ClientConfigInterface $config
     * @param string $network
     * @param string $organization
     * @return ClientInterface
     * @throws \AmericanExpress\HyperledgerFabricClient\Exception\RuntimeException
     * @throws \AmericanExpress\HyperledgerFabricClient\Exception\InvalidArgumentException
     */
    public static function fromConfig(
        ClientConfigInterface $config,
        string $network,
        string $organization
    ): ClientInterface {
        $organizationOptions = $config->getOrganization($network, $organization);
        if ($organizationOptions === null) {
            throw new UnexpectedValueException(sprintf(
                'Unable to load options for organization `%s` in network `%s`.',
                $organization,
                $network
            ));
        }

        $identity = SerializedIdentityFactory::fromFile(
            $organizationOptions->getMspId(),
            new \SplFileObject($organizationOptions->getAdminCerts())
        );

        $signatory = new MdanterEccSignatory($config->getHashAlgorithm());

        $endorserClients = new EndorserClientManager();

        return new Client($identity, $signatory, $endorserClients, $config);
    }
}
