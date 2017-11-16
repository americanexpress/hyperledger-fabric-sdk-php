<?php
declare(strict_types=1);

namespace AmericanExpressTest\Integration\TestAsset;

use AmericanExpress\HyperledgerFabricClient\ChaincodeQueryParams;
use AmericanExpress\HyperledgerFabricClient\ChannelContext;
use AmericanExpress\HyperledgerFabricClient\ChannelFactory;
use AmericanExpress\HyperledgerFabricClient\Config\ClientConfigFactory;
use function igorw\get_in;

class E2EUtils
{
    /**
     * @param string $org
     * @return string
     */
    public function queryChaincode(string $org)
    {
        $queryParams = $this->getQueryParam();
        $config = ClientConfigFactory::fromFile(new \SplFileObject(__DIR__ . '/../config.php'));
        $channel = ChannelFactory::fromConfig($config);
        $orgConfig = $config->getIn(['test-network', $org]);
        $channelContext = new ChannelContext([
            'host' => get_in($orgConfig, ['peer1', 'requests']),
            'mspId' => get_in($orgConfig, ['mspid']),
            'adminCerts' => new \SplFileObject(get_in($orgConfig, ['admin_certs'])),
            'privateKey' => new \SplFileObject(get_in($orgConfig, ['private_key'])),
        ]);
        $fabricProposal = $channel->queryByChainCode($channelContext, $queryParams);
        return $fabricProposal->getPayload();
    }

    /**
     * @return ChaincodeQueryParams
     */
    public function getQueryParam()
    {
        return new ChaincodeQueryParams([
            'CHAINCODE_NAME' => 'example_cc',
            'CHAINCODE_PATH' => 'github.com/example_cc',
            'CHAINCODE_VERSION' => '1',
            'CHANNEL_ID' => 'foo',
            'ARGS' => [
                'invoke',
                'query',
                'a',
            ],
        ]);
    }
}
