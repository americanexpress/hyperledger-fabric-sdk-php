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

namespace AmericanExpressTest\HyperledgerFabricClient\Chaincode;

use AmericanExpress\HyperledgerFabricClient\Nonce\NonceGeneratorInterface;
use AmericanExpress\HyperledgerFabricClient\ProtoFactory\ChaincodeHeaderExtensionFactory;
use AmericanExpress\HyperledgerFabricClient\ProtoFactory\ChaincodeIdFactory;
use AmericanExpress\HyperledgerFabricClient\ProtoFactory\ChaincodeProposalPayloadFactory;
use AmericanExpress\HyperledgerFabricClient\ProtoFactory\ChannelHeaderFactory;
use AmericanExpress\HyperledgerFabricClient\ProtoFactory\HeaderFactory;
use AmericanExpress\HyperledgerFabricClient\ProtoFactory\ProposalFactory;
use AmericanExpress\HyperledgerFabricClient\ProtoFactory\SerializedIdentityFactory;
use AmericanExpress\HyperledgerFabricClient\ProtoFactory\SignatureHeaderFactory;
use AmericanExpress\HyperledgerFabricClient\ProtoFactory\TimestampFactory;
use AmericanExpress\HyperledgerFabricClient\Transaction\TransactionIdentifierGenerator;
use Hyperledger\Fabric\Protos\Peer\Proposal;
use org\bovigo\vfs\vfsStream;
use org\bovigo\vfs\vfsStreamFile;
use PHPUnit\Framework\TestCase;

abstract class AbstractChaincodeTest extends TestCase
{
    /**
     * @var vfsStreamFile
     */
    protected $privateKey;

    /**
     * @var \SplFileObject
     */
    protected $privateKeyFile;

    protected function setUp()
    {
        $files = vfsStream::setup('test');

        $this->privateKey = vfsStream::newFile('foo');
        $this->privateKey->setContent(<<<'TAG'
-----BEGIN PRIVATE KEY-----
MIGHAgEAMBMGByqGSM49AgEGCCqGSM49AwEHBG0wawIBAQQghnA7rdgbZi/wndus
iXjyf0KgE6OKZjQ+5INjwelRAC6hRANCAASb3u+hY+U/FZvhYDN6d08HJ1v56UJU
yz/n2NHyJgTg6kC05AaJMeGIinEF0JeJtRDNVQGzoQJQYjnzUTS9FvGh
-----END PRIVATE KEY-----
TAG
        );
        $files->addChild($this->privateKey);
        $this->privateKeyFile = new \SplFileObject($this->privateKey->url());
    }

    private function createMockTransactionIdentifierGenerator(): TransactionIdentifierGenerator
    {
        return new TransactionIdentifierGenerator(
            new class implements NonceGeneratorInterface {
                public function generateNonce(): string
                {
                    return 'u23m5k4hf86j';
                }
            }
        );
    }

    protected function createChaincodeProposal(string $dateTime, \SplFileObject $privateKeyFile): Proposal
    {
        $transactionContextFactory = $this->createMockTransactionIdentifierGenerator();
        $identity = SerializedIdentityFactory::fromFile('1234', $privateKeyFile);
        $transactionContext = $transactionContextFactory->fromSerializedIdentity($identity);

        $channelHeader = ChannelHeaderFactory::create('MyChannelId');
        $channelHeader->setTxId($transactionContext->getId());
        $channelHeader->setEpoch(0);
        $channelHeader->setTimestamp(TimestampFactory::fromDateTime(new \DateTime($dateTime)));

        $chaincodeId = ChaincodeIdFactory::create(
            'MyChaincodePath',
            'MyChaincodeName',
            'MyChaincodeVersion'
        );

        $chaincodeHeaderExtension = ChaincodeHeaderExtensionFactory::fromChaincodeId($chaincodeId);
        $channelHeader->setExtension($chaincodeHeaderExtension->serializeToString());

        $header = HeaderFactory::create(SignatureHeaderFactory::create(
            $identity,
            $transactionContext->getNonce()
        ), $channelHeader);

        $chaincodeProposalPayload = ChaincodeProposalPayloadFactory::fromChaincodeInvocationSpecArgs([]);
        return ProposalFactory::create($header, $chaincodeProposalPayload->serializeToString());
    }

    /**
     * @return array[]
     */
    protected function loadStaticData(): array
    {
        $contents = file_get_contents(__DIR__ . '/../../_files/signed-proposals.json');

        $json = json_decode($contents, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new \RuntimeException(json_last_error_msg(), json_last_error());
        }

        return $json;
    }

    /**
     * @return array[]
     */
    public function getChainCodeProposalDataset(): array
    {
        $data = $this->loadStaticData();

        return array_map(
            function ($value) {
                return array_intersect_key(
                    $value,
                    array_flip(['dateTime', 'proposalHeader', 'proposalPayload', 'proposalExtension'])
                );
            },
            $data
        );
    }

    /**
     * @return \SplFileObject
     */
    protected function getPrivateKeyFile(): \SplFileObject
    {
        return $this->privateKeyFile;
    }
}
