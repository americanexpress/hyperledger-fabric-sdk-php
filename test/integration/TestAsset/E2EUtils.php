<?php
declare(strict_types=1);

namespace AmericanExpressTest\Integration\TestAsset;

use AmericanExpress\HyperledgerFabricClient\AppConf as AppConfig;
use AmericanExpress\HyperledgerFabricClient\Channel;
use AmericanExpress\HyperledgerFabricClient\Utils;

class E2EUtils
{
    /**
     * @param string $org
     * @return string
     */
    public function queryChaincode(string $org)
    {
        $queryParams = $this->getQueryParam();
        $utils = new Utils();
        AppConfig::setAppConfigPath("test/integration/config.json");
        $connect = $utils->fabricConnect($org);
        $channel = new Channel();
        $fabricProposal = $channel->queryByChainCode($org, $connect, $queryParams);
        return $fabricProposal->getPayload();
    }

    /**
     * @return mixed[]
     * set Query parameters
     */
    public function getQueryParam()
    {
        return [
            'CHAINCODE_NAME' => 'example_cc',
            'CHAINCODE_PATH' => 'github.com/example_cc',
            'CHAINCODE_VERSION' => '1',
            'CHANNEL_ID' => 'foo',
            'ARGS' => [
                'invoke',
                'query',
                'a',
            ],
        ];
    }
}
