<?php

/**
 * Copyright 2017 American Express Travel Related Services Company, Inc.
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 * http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express
 * or implied. See the License for the specific language governing
 * permissions and limitations under the License.
 */

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
