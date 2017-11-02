<?php

namespace AmericanExpressTest\Integration\TestAsset;

use AmericanExpress\FabricClient\AppConf as AppConfig;
use AmericanExpress\FabricClient\Channel;
use AmericanExpress\FabricClient\Utils;

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
    public function queryChaincode($org)
    {
        $queryParams = $this->getQueryParam();
        $utils = new Utils();
        AppConfig::setAppConfigPath("/../test/integration/config.json");
        $connect = $utils->fabricConnect($org);
        $channel = new Channel();
        $fabricProposal = $channel->queryByChainCode($org, $connect, $queryParams);
        return $fabricProposal->getPayload();
    }

    /**
     * @return array
     * set Query parameters
     */
    public function getQueryParam()
    {
        $queryParams = array();
        $queryParams["CHAINCODE_NAME"] = "example_cc";
        $queryParams["CHAINCODE_PATH"] = "github.com/example_cc";
        $queryParams["CHAINCODE_VERSION"] = "1";
        $queryParams["CHANNEL_ID"] = "foo";
        $queryParams["ARGS"] = ["invoke","query","a"];
        return $queryParams;
    }
}
