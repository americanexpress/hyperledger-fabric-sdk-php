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

use AmericanExpress\HyperledgerFabricClient\Organization\OrganizationOptions;
use AmericanExpress\HyperledgerFabricClient\User\UserContext;
use Hyperledger\Fabric\Protos\MSP\SerializedIdentity;
use PHPUnit\Framework\TestCase;

/**
 * @covers \AmericanExpress\HyperledgerFabricClient\User\UserContext
 */
class UserContextTest extends TestCase
{
    public function testIdentity()
    {
        $identity = new SerializedIdentity();

        $organization = new OrganizationOptions();

        $sut = new UserContext($identity, $organization);

        self::assertSame($identity, $sut->getIdentity());
    }

    public function testOrganization()
    {
        $identity = new SerializedIdentity();

        $organization = new OrganizationOptions();

        $sut = new UserContext($identity, $organization);

        self::assertSame($organization, $sut->getOrganization());
    }
}
