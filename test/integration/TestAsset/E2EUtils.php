<?php
declare(strict_types=1);

namespace AmericanExpressTest\Integration\TestAsset;

use AmericanExpress\HyperledgerFabricClient\ChaincodeQueryParams;
use AmericanExpress\HyperledgerFabricClient\ChannelContext;
use AmericanExpress\HyperledgerFabricClient\ChannelFactory;
use AmericanExpress\HyperledgerFabricClient\Config\ClientConfigFactory;

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
        $channelContext = new ChannelContext([
            'host' => $config->getIn(['test-network', $org, 'peer1', 'requests']),
            'mspId' => $config->getIn(['test-network', $org, 'mspid']),
            'adminCerts' => new \SplFileObject($config->getIn(['test-network', $org, 'admin_certs'])),
            'epoch' => $config->getIn(['epoch']),
            'privateKey' => new \SplFileObject($config->getIn(['test-network', $org, 'private_key'])),
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
