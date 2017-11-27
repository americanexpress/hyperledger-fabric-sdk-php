<?php
declare(strict_types=1);

namespace AmericanExpress\HyperledgerFabricClient\User;

use AmericanExpress\HyperledgerFabricClient\Organization\OrganizationOptionsInterface;
use Hyperledger\Fabric\Protos\MSP\SerializedIdentity;

interface UserContextInterface
{
    /**
     * @return OrganizationOptionsInterface
     */
    public function getOrganization(): OrganizationOptionsInterface;

    /**
     * @return SerializedIdentity
     */
    public function getIdentity(): SerializedIdentity;
}