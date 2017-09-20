<?php

class Config
{
    public static function getConfig($key)
    {
        $jsonStr = file_get_contents("../../test/integration/config.json");
        $config = json_decode($jsonStr);

        return $config->$key;
    }

    public static function loadDefaults($key)
    {

        $jsonStr = file_get_contents("../../fabric-client/config/default.json");
        $config = json_decode($jsonStr);

        return $config->$key;
    }

}
