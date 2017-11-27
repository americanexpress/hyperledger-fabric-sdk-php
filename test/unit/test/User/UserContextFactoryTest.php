<?php
declare(strict_types=1);

namespace AmericanExpressTest\HyperledgerFabricClient\User;

use AmericanExpress\HyperledgerFabricClient\Config\ClientConfig;
use AmericanExpress\HyperledgerFabricClient\Organization\OrganizationOptionsInterface;
use AmericanExpress\HyperledgerFabricClient\User\UserContext;
use AmericanExpress\HyperledgerFabricClient\User\UserContextFactory;
use Hyperledger\Fabric\Protos\MSP\SerializedIdentity;
use PHPUnit\Framework\TestCase;

/**
 * @covers \AmericanExpress\HyperledgerFabricClient\User\UserContextFactory
 */
class UserContextFactoryTest extends TestCase
{
    public function testFromConfig()
    {
        $config = new ClientConfig([
            'organizations' => [
                [
                    'name' => 'peerOrg1',
                    'mspid' => 'Org1MSP',
                    'adminCerts' => __FILE__,
                ],
            ],
        ]);

        $result = UserContextFactory::fromConfig($config, 'peerOrg1');

        self::assertInstanceOf(UserContext::class, $result);
        self::assertInstanceOf(SerializedIdentity::class, $result->getIdentity());
        self::assertInstanceOf(OrganizationOptionsInterface::class, $result->getOrganization());
        self::assertSame('peerOrg1', $result->getOrganization()->getName());
        self::assertSame('Org1MSP', $result->getOrganization()->getMspId());
        self::assertSame(__FILE__, $result->getOrganization()->getAdminCerts());
    }

    public function testFromConfigWithDefaultOrganization()
    {
        $config = new ClientConfig([
            'organizations' => [
                [
                    'name' => 'peerOrg1',
                    'mspid' => 'Org1MSP',
                    'adminCerts' => __FILE__,
                ],
            ],
        ]);

        $result = UserContextFactory::fromConfig($config);

        self::assertInstanceOf(UserContext::class, $result);
        self::assertInstanceOf(SerializedIdentity::class, $result->getIdentity());
        self::assertInstanceOf(OrganizationOptionsInterface::class, $result->getOrganization());
        self::assertSame('peerOrg1', $result->getOrganization()->getName());
        self::assertSame('Org1MSP', $result->getOrganization()->getMspId());
        self::assertSame(__FILE__, $result->getOrganization()->getAdminCerts());
    }

    public function testFromConfigWithMultipleOrganizations()
    {
        $config = new ClientConfig([
            'organizations' => [
                [
                    'name' => 'peerOrg1',
                    'mspid' => 'Org1MSP',
                    'adminCerts' => __FILE__,
                ],
                [
                    'name' => 'peerOrg2',
                    'mspid' => 'Org2MSP',
                    'adminCerts' => __FILE__,
                ],
            ],
        ]);

        $result = UserContextFactory::fromConfig($config, 'peerOrg2');

        self::assertInstanceOf(UserContext::class, $result);
        self::assertInstanceOf(SerializedIdentity::class, $result->getIdentity());
        self::assertInstanceOf(OrganizationOptionsInterface::class, $result->getOrganization());
        self::assertSame('peerOrg2', $result->getOrganization()->getName());
        self::assertSame('Org2MSP', $result->getOrganization()->getMspId());
        self::assertSame(__FILE__, $result->getOrganization()->getAdminCerts());
    }

    /**
     * @expectedException \AmericanExpress\HyperledgerFabricClient\Exception\UnexpectedValueException
     */
    public function testFromConfigWithInvalidOrganization()
    {
        $config = new ClientConfig([
            'organizations' => [
                [
                    'name' => 'peerOrg1',
                    'mspid' => 'Org1MSP',
                    'adminCerts' => __FILE__,
                ],
            ],
        ]);

        UserContextFactory::fromConfig($config, 'FooBar');
    }
}
