<?php
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
