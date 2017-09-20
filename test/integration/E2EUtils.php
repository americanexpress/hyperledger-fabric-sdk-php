<?php

use fabric\sdk;

class E2EUtils
{

    private static $chainCodeName =  "sparrow_txn_cc";
    private static $chainCodePath =  "github.com/sparrow_txn";
    private static $chainCodeVersion ="2";
    private static  $chainChannelId = "foo";

    public function queryChaincode($org, $version, $value, $t, $transientMap)
    {
        $utils = new fabric\sdk\Utils();

//        $nounce = $utils::getNonce();

        $connect = $utils->FabricConnect();

        $channel = new fabric\sdk\Channel();

        $fabricProposal = $channel->queryByChainCode($connect, self::$chainChannelId,self::$chainCodeName,self::$chainCodePath,self::$chainCodeVersion);


    }
}