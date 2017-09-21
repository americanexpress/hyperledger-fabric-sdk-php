<?php

use fabric\sdk;

class E2EUtils
{

    private static $chainCodeName =  "example_cc";
    private static $chainCodePath =  "github.com/example_cc";
    private static $chainCodeVersion ="1";
    private static  $chainChannelId = "foo";

    public function queryChaincode($org, $version, $value, $t, $transientMap)
    {
        $utils = new fabric\sdk\Utils();

        Config::setAppConfigPath("/../test/integration/config.json");

        $connect = $utils->FabricConnect($org);

        $channel = new fabric\sdk\Channel();

        $fabricProposal = $channel->queryByChainCode($org, $connect, self::$chainChannelId,self::$chainCodeName,self::$chainCodePath,self::$chainCodeVersion);


    }
}