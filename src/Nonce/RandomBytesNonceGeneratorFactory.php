<?php
declare(strict_types=1);

namespace AmericanExpress\HyperledgerFabricClient\Nonce;

use AmericanExpress\HyperledgerFabricClient\Config\ClientConfigInterface;

class RandomBytesNonceGeneratorFactory
{
    /**
     * @param ClientConfigInterface $config
     * @return RandomBytesNonceGenerator
     */
    public static function fromConfig(ClientConfigInterface $config): RandomBytesNonceGenerator
    {
        return new RandomBytesNonceGenerator($config->getIn(['nonce-size']));
    }
}
