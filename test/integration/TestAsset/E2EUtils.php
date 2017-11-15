<?php
declare(strict_types=1);

namespace AmericanExpressTest\Integration\TestAsset;

use AmericanExpress\HyperledgerFabricClient\ChannelFactory;

class E2EUtils
{
    /**
     * @param string $org
     * @return string
     */
    public function queryChaincode(string $org)
    {
        $queryParams = $this->getQueryParam();
        $channel = ChannelFactory::fromConfigFile(new \SplFileObject(__DIR__ . '/../config.php'));
        $fabricProposal = $channel->queryByChainCode($queryParams, $org);
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
