<?php
declare(strict_types=1);

namespace AmericanExpress\HyperledgerFabricClient\Cryptography;

use AmericanExpress\HyperledgerFabricClient\Config\ClientConfigInterface;

class MdanterEccFactory
{
    /**
     * @param ClientConfigInterface $config
     * @return MdanterEcc
     */
    public static function fromConfig(ClientConfigInterface $config): MdanterEcc
    {
        return new MdanterEcc($config->getIn(['nonce-size']), $config->getIn(['crypto-hash-algo']));
    }
}
