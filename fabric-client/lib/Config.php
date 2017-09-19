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
//
//        // read the SDK config default json and return
//        $jsonStr = file_get_contents("../config/default.json");
//        $config = json_decode($jsonStr);
//
//        echo var_dump($config);
//        return $config->$key;
    }

}
