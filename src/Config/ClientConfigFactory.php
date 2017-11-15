<?php
declare(strict_types=1);

namespace AmericanExpress\HyperledgerFabricClient\Config;

class ClientConfigFactory
{
    /**
     * @param \SplFileObject $file
     * @return ClientConfig
     */
    public static function fromFile(\SplFileObject $file)
    {
        $path = $file->getPathname();

        $config = require $path;

        return new ClientConfig($config);
    }
}
