<?php
declare(strict_types=1);

namespace AmericanExpress\HyperledgerFabricClient;

class ClientConfig implements ClientConfigInterface
{
    // TODO These values should be overridable by consumers. Check config first before returning defaults.
    private const DEFAULT_CONFIG = [
        'timeout' => 5000,
        'epoch' => 0,
        'crypto-hash-algo' => 'sha256',
        'nonce-size'  => 24
    ];

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
        $this->config = $config;
    }

    /**
     * @param string|null $jsonFilePath
     * @return ClientConfig
     * Temporary function, to be used until static functions are removed from this class.
     */
    public static function getInstance(string $jsonFilePath = null): ClientConfig
    {
        if (!self::$instance) {
            self::$instance = self::createFromJsonFile($jsonFilePath);
        }

        return self::$instance;
    }

    /**
     * TODO: Replace usages of this function with calls to `getDefault` on an injected config instance.
     * @param string $key
     * Method to get default SDK configuration.
     * @return mixed|null
     */
    public static function loadDefaults(string $key)
    {
        return self::getInstance()->getDefault($key);
    }

    /**
     * TODO: Merge default config before instantiating, and replace calls to getDefault with getIn.
     * @param string $key
     * Method to get default SDK configuration.
     * @return mixed|null
     */
    public function getDefault(string $key)
    {
        if (array_key_exists($key, self::DEFAULT_CONFIG)) {
            return self::DEFAULT_CONFIG[$key];
        }

        return null;
    }

    /**
     * @param string[] $keys
     * @param mixed $default
     * @return mixed|null
     */
    public function getIn(array $keys = [], $default = null)
    {
        $config = $this->config;

        while ($keys) {
            $key = array_shift($keys);
            if (!array_key_exists($key, $config)) {
                return $default;
            }

            $config = $config[$key];
        }

        return $config;
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
}
