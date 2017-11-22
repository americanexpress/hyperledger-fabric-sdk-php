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

return [
    'test-network' => [
        'orderer' => [
            'url' => 'localhost:7050',
            'server-hostname' => 'orderer.example.com',
            'tls_cacerts' => __DIR__ . '/../../test/fixture/sdkintegration/e2e-2Orgs/channel/crypto-config/ordererOrganizations/example.com/orderers/orderer.example.com/msp/tlscacerts/ca.example.com-cert.pem'
        ],
        'org1' => [
            'name' => 'peerOrg1',
            'mspid' => 'Org1MSP',
            'ca' => [
                'url' => 'https://localhost:7054',
                'name' => 'ca-org1'
            ],
            'admin_certs' => __DIR__ . '/../../test/fixture/sdkintegration/e2e-2Orgs/channel/crypto-config/peerOrganizations/org1.example.com/users/Admin@org1.example.com/msp/admincerts/Admin@org1.example.com-cert.pem',
            'private_key' => __DIR__ . '/../../test/fixture/sdkintegration/e2e-2Orgs/channel/crypto-config/peerOrganizations/org1.example.com/users/Admin@org1.example.com/msp/keystore/6b32e59640c594cf633ad8c64b5958ef7e5ba2a205cfeefd44a9e982ce624d93_sk',
            'peers' => [
                [
                    'name' => 'peer1',
                    'requests' => 'localhost:7051',
                    'events' => 'localhost:7053',
                    'server-hostname' => 'peer0.org1.example.com',
                    'tls_cacerts' => __DIR__ . '/../../test/fixture/sdkintegration/e2e-2Orgs/channel/crypto-config/peerOrganizations/org1.example.com/peers/peer0.org1.example.com/tlscacerts/org1.example.com-cert.pem'
                ],
            ],
        ],
        'org2' => [
            'name' => 'peerOrg2',
            'mspid' => 'Org2MSP',
            'ca' => [
                'url' => 'https://localhost:8054',
                'name' => 'ca-org2'
            ],
            'peers' => [
                [
                    'name' => 'peer1',
                    'requests' => 'localhost:8051',
                    'events' => 'localhost:8053',
                    'server-hostname' => 'peer0.org2.example.com',
                    'tls_cacerts' => __DIR__ . '/../../test/fixture/sdkintegration/e2e-2Orgs/channel/crypto-config/peerOrganizations/org2.example.com/peers/peer0.org2.example.com/tlscacerts/org2.example.com-cert.pem'
                ],
            ]
        ]
    ]
];
