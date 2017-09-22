<?php

use fabric\sdk;

class E2EUtils
{
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
        $queryParams[CHAINCODE_NAME] = "example_cc";
        $queryParams[CHAINCODE_PATH] = "github.com/example_cc";
        $queryParams[CHAINCODE_VERSION] = "1";
        $queryParams[CHANNEL_ID] = "foo";
        $queryParams[ARGS] = ["invoke","query","a"];
        return $queryParams;
    }
}