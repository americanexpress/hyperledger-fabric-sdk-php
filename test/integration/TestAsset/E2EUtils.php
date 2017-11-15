<?php
declare(strict_types=1);

namespace AmericanExpressTest\Integration\TestAsset;

use AmericanExpress\HyperledgerFabricClient\ClientConfig;
use AmericanExpress\HyperledgerFabricClient\Channel;

class E2EUtils
{
    /**
     * @param string $org
     * @return string
     */
    public function queryChaincode(string $org)
    {
        $queryParams = $this->getQueryParam();
        $config = new ClientConfig(require __DIR__ . '/../config.php');
        $channel = new Channel($config);
        $fabricProposal = $channel->queryByChainCode($org, $queryParams);
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
