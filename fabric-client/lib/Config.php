<?php

class Config
{
    static $org = null;

    static $appConfigPath = null;

    function __construct()
    {

    }

    /**
     * Method to set default SDK configuration.
     **/
    public static function loadDefaults($key)
    {
        $jsonStr = file_get_contents(DEFAULT_JSON_PATH);
        $config = json_decode($jsonStr);


        return $config->$key;
    }

    /**
     * Method to set org configuration.
     **/
    public static function getOrgConfig($org)
    {
        $configPath = trim(ROOTPATH . self::$appConfigPath);
        $jsonStr = file_get_contents($configPath);
        $config = json_decode($jsonStr, true);
        self::$org = $org;
        return $config[TEST_NETWORK][$org];
    }

    public static function setAppConfigPath($path){
        self::$appConfigPath = $path . "  ";
    }
}
