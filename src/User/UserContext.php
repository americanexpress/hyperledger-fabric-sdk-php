<?php
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
