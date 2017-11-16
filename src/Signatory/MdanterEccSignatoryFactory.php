<?php
declare(strict_types=1);

namespace AmericanExpress\HyperledgerFabricClient\Signatory;

use AmericanExpress\HyperledgerFabricClient\Config\ClientConfigInterface;
use AmericanExpress\HyperledgerFabricClient\ValueObject\HashAlgorithm;

class MdanterEccSignatoryFactory
{
    /**
     * @param ClientConfigInterface $config
     * @return MdanterEccSignatory
     */
    public static function fromConfig(ClientConfigInterface $config): MdanterEccSignatory
    {
        return new MdanterEccSignatory(HashAlgorithm::fromConfig($config));
    }
}
