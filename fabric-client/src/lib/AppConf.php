<?php
declare(strict_types=1);

namespace AmericanExpress\HyperledgerFabricClient;

class AppConf
{
    private static $org = null;
    private static $appConfigPath = null;

    /**
     * @param string $key
     * Method to set default SDK configuration.
     * @return mixed
     */
    public static function loadDefaults(string $key)
    {
        $jsonStr = file_get_contents(__DIR__."/../config/default.json");
        $config = json_decode($jsonStr);

        return $config->$key;
    }

    /**
     * @param string $org
     * Method to set org configuration.
     * @return mixed
     **/
    public static function getOrgConfig(string $org)
    {
        $configPath = trim(ROOTPATH . self::$appConfigPath);
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
        self::$appConfigPath = $path . "  ";
    }
}
