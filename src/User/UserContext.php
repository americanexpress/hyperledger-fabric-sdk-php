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

use AmericanExpress\HyperledgerFabricClient\Organization\OrganizationOptionsInterface;
use Hyperledger\Fabric\Protos\MSP\SerializedIdentity;

final class UserContext implements UserContextInterface
{
    /**
     * @var SerializedIdentity
     */
    private $identity;

    /**
     * @var OrganizationOptionsInterface
     */
    private $organization;

    /**
     * UserContext constructor.
     * @param SerializedIdentity $identity
     * @param OrganizationOptionsInterface $organization
     */
    public function __construct(SerializedIdentity $identity, OrganizationOptionsInterface $organization)
    {
        $this->identity = $identity;
        $this->organization = $organization;
    }

    /**
     * @return OrganizationOptionsInterface
     */
    public function getOrganization(): OrganizationOptionsInterface
    {
        return $this->organization;
    }

    /**
     * @return SerializedIdentity
     */
    public function getIdentity(): SerializedIdentity
    {
        return $this->identity;
    }
}
