<?php
declare(strict_types=1);

namespace AmericanExpress\HyperledgerFabricClient;

use AmericanExpress\HyperledgerFabricClient\Config\ClientConfigFactory;
use AmericanExpress\HyperledgerFabricClient\Config\ClientConfigInterface;
use AmericanExpress\HyperledgerFabricClient\Cryptography\MdanterEccFactory;

class ChannelFactory
{
    /**
     * @param ClientConfigInterface $config
     * @return Channel
     */
    public static function fromConfig(ClientConfigInterface $config): Channel
    {
        $endorserClients = new EndorserClientManager();

        $hash = MdanterEccFactory::fromConfig($config);

        return new Channel($endorserClients, $hash);
    }

    /**
     * @param \SplFileObject $file
     * @return Channel
     */
    public static function fromConfigFile(\SplFileObject $file)
    {
        $config = ClientConfigFactory::fromFile($file);

        return self::fromConfig($config);
    }
}
