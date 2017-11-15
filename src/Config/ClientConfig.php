<?php
declare(strict_types=1);

namespace AmericanExpress\HyperledgerFabricClient\Config;

use function igorw\get_in;

class ClientConfig implements ClientConfigInterface
{
    /**
     * @var array
     */
    private $config;

    /**
     * ClientConfig constructor.
     * @param array $config
     */
    public function __construct(array $config)
    {
        $this->config = array_merge(
            [
                'timeout' => 5000,
                'epoch' => 0,
                'crypto-hash-algo' => 'sha256',
                'nonce-size'  => 24,
            ],
            $config
        );
    }

    /**
     * @param string[] $keys
     * @param mixed $default
     * @return mixed|null
     */
    public function getIn(array $keys = [], $default = null)
    {
        return get_in($this->config, $keys, $default);
    }
}
