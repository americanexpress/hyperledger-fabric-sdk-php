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

namespace AmericanExpress\HyperledgerFabricClient\User;

use AmericanExpress\HyperledgerFabricClient\Config\ClientConfigInterface;
use AmericanExpress\HyperledgerFabricClient\Exception\UnexpectedValueException;
use AmericanExpress\HyperledgerFabricClient\Organization\OrganizationOptionsInterface;
use AmericanExpress\HyperledgerFabricClient\ProtoFactory\SerializedIdentityFactory;

class UserContextFactory
{
    /**
     * @param ClientConfigInterface $config
     * @param string|null $organization
     * @return UserContext
     */
    public static function fromConfig(
        ClientConfigInterface $config,
        string $organization = null
    ): UserContext {
        $organizationOptions = self::getOrganization($config, $organization);

        $identity = SerializedIdentityFactory::fromFile(
            $organizationOptions->getMspId(),
            new \SplFileObject($organizationOptions->getAdminCerts())
        );

        return new UserContext($identity, $organizationOptions);
    }

    /**
     * @param ClientConfigInterface $config
     * @param string|null $organization
     * @return OrganizationOptionsInterface
     * @throws UnexpectedValueException
     */
    private static function getOrganization(
        ClientConfigInterface $config,
        string $organization = null
    ): OrganizationOptionsInterface {
        if ($organization) {
            $options = $config->getOrganizationByName($organization);

            if ($options === null) {
                throw new UnexpectedValueException(sprintf(
                    'Unable to load options for organization `%s`.',
                    $organization
                ));
            }

            return $options;
        }

        return $config->getDefaultOrganization();
    }
}
