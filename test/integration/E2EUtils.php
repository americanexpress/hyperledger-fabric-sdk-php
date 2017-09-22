<?php

use fabric\sdk;

define("CHAINCODE_NAME","example_cc");
define("CHAINCODE_PATH","github.com/example_cc");
define("CHAINCODE_VERSION","1");
define("CHANNELID","foo");
class E2EUtils
{
    private static $args = ["invoke","query","a"];

    /**
     * @param $org
     * @param $version
     * @param $value
     * @param $t
     * @param $transientMap
     * Query given chaincode with given args.
     */
    public function queryChaincode($org, $version, $value, $t, $transientMap)
    {
        $queryParams = $this->getQueryParam();
        $utils = new fabric\sdk\Utils();
        Config::setAppConfigPath("/../test/integration/config.json");
        $connect = $utils->FabricConnect($org);
        $channel = new fabric\sdk\Channel();
        $fabricProposal = $channel->queryByChainCode($org, $connect, $queryParams);
       print_r($fabricProposal);
    }

    /**
     * @return array
     * set Query parameters
     */
    public function getQueryParam(){
        $queryParams = array();
        $queryParams['chainCodeName'] = CHAINCODE_NAME;
        $queryParams['chainCodePath'] = CHAINCODE_PATH;
        $queryParams['chainCodeVersion'] = CHAINCODE_VERSION;
        $queryParams['channelId'] = CHANNELID;
        $queryParams['args'] = ["invoke","query","a"];
        return $queryParams;
    }
}