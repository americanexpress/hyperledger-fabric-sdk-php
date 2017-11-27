<?php
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
