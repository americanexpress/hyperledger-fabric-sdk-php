<?php
declare(strict_types=1);

namespace AmericanExpress\HyperledgerFabricClient\Signatory;

use AmericanExpress\HyperledgerFabricClient\Config\ClientConfigInterface;

class MdanterEccSignatoryFactory
{
    /**
     * @param ClientConfigInterface $config
     * @return MdanterEccSignatory
     */
    public static function fromConfig(ClientConfigInterface $config): MdanterEccSignatory
    {
        return new MdanterEccSignatory($config->getIn(['crypto-hash-algo']));
    }
}
