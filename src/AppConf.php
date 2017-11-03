<?php
declare(strict_types=1);

namespace AmericanExpress\HyperledgerFabricClient;

class AppConf
{
    private const DEFAULT_CONFIG = [
        'timeout' => 5000,
        'epoch' => 0,
        'crypto-hash-algo' => 'sha256',
        'nonce-size' => 24
    ];

    private static $org = null;
    private static $appConfigPath = null;

    /**
     * @param string $key
     * Method to set default SDK configuration.
     * @return mixed
     */
    public static function loadDefaults(string $key)
    {
        if (array_key_exists($key, self::DEFAULT_CONFIG)) {
            return self::DEFAULT_CONFIG[$key];
        }

        return null;
    }

    /**
     * @param string $org
     * Method to set org configuration.
     * @return mixed
     **/
    public static function getOrgConfig(string $org)
    {
        $configPath = rtrim(ROOTPATH, '/') . '/' . ltrim(self::$appConfigPath, '/');
        $jsonStr = file_get_contents($configPath);
        $config = json_decode($jsonStr, true);
        self::$org = $org;
        return $config["test-network"][$org];
    }

    /**
     * @param string $path
     */
    public static function setAppConfigPath(string $path)
    {
        self::$appConfigPath = $path;
    }
}
