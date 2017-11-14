<?php
declare(strict_types=1);

namespace AmericanExpress\HyperledgerFabricClient;

use function igorw\get_in;

class ClientConfig implements ClientConfigInterface
{
    /**
     * @var array
     */
    private $config;

    /**
     * @var ClientConfig
     * @deprecated Replace all static function calls ASAP, and drop this static reference.
     */
    private static $instance;

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
     * @param array $config
     * @return ClientConfig
     * Temporary function, to be used until static functions are removed from this class.
     */
    public static function getInstance(array $config = []): ClientConfig
    {
        if (!self::$instance) {
            self::$instance = new ClientConfig($config);
        }

        return self::$instance;
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

    /**
     * @param string $jsonFilePath
     * @param string $rootPath
     * @return ClientConfig
     */
    public static function createFromJsonFile(string $jsonFilePath, $rootPath = ROOTPATH): ClientConfig
    {
        if (!file_exists($jsonFilePath)) {
            $jsonFilePath = rtrim($rootPath, '/') . '/' . ltrim($jsonFilePath, '/');
        }

        $config = file_get_contents($jsonFilePath);

        // TODO Handle decoding errors.
        return new ClientConfig(json_decode($config, true) ?: []);
    }

    /**
     * TODO: Replace usages of this function with calls to `getIn` on an injected config instance.
     * Method to set org configuration.
     * @param string $org
     * @param string $network
     * @return mixed|null
     */
    public static function getOrgConfig(string $org, string $network = 'test-network')
    {
        return self::getInstance()->getIn([$network, $org], null);
    }
}
