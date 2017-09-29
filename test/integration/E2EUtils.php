<?php
use AmericanExpress\FabricClient\Utils;
use AmericanExpress\FabricClient\Config as AppConfig;
use AmericanExpress\FabricClient\Channel;

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
        $utils = new Utils();
        AppConfig::setAppConfigPath("/../test/integration/config.json");
        $connect = $utils->FabricConnect($org);
        $channel = new AmericanExpress\FabricClient\Channel();
        $fabricProposal = $channel->queryByChainCode($org, $connect, $queryParams);
        print_r($fabricProposal);
    }

    /**
     * @return array
     * set Query parameters
     */
    public function getQueryParam()
    {
        $queryParams = array(
            "CHAINCODE_NAME" => "example_cc", "CHAINCODE_PATH" => "github.com/example_cc", "CHAINCODE_VERSION" => "1", "CHANNEL_ID" => "foo",
            "ARGS" => "[\"invoke\",\"query\",\"a\"]"
        );
        return $queryParams;
    }
}