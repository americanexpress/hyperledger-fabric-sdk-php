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

namespace AmericanExpressTest\HyperledgerFabricClient\Signatory;

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
use AmericanExpress\HyperledgerFabricClient\Signatory\MdanterEccSignatory;
use AmericanExpress\HyperledgerFabricClient\Transaction\TransactionIdentifierGenerator;
use AmericanExpressTest\HyperledgerFabricClient\Chaincode\AbstractChaincodeTest;
use Hyperledger\Fabric\Protos\Peer\Proposal;
use Hyperledger\Fabric\Protos\Peer\SignedProposal;
use org\bovigo\vfs\vfsStream;
use org\bovigo\vfs\vfsStreamFile;

/**
 * @covers \AmericanExpress\HyperledgerFabricClient\Signatory\MdanterEccSignatory
 */
class MdanterEccSignatoryTest extends AbstractChaincodeTest
{
    /**
     * @var vfsStreamFile
     */
    private $privateKey;

    /**
     * @var \SplFileObject
     */
    private $privateKeyFile;

    /**
     * @var MdanterEccSignatory
     */
    private $sut;

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

        $this->sut = new MdanterEccSignatory();
    }

    public function testSignEmptyProposal()
    {
        $result = $this->sut->signProposal(new Proposal(), $this->privateKeyFile);

        self::assertInstanceOf(SignedProposal::class, $result);
        self::assertInternalType('string', $result->getProposalBytes());
        self::assertEmpty($result->getProposalBytes());
        self::assertInternalType('string', $result->getSignature());
        self::assertNotEmpty($result->getSignature());
    }

    public function testSignProposal()
    {
        $proposal = new Proposal();
        $proposal->setHeader('HEADER-STRING');
        $proposal->setPayload('PAYLOAD-STRING');
        $result = $this->sut->signProposal($proposal, $this->privateKeyFile);

        self::assertEquals(
            'Cg1IRUFERVItU1RSSU5HEg5QQVlMT0FELVNUUklORw==',
            base64_encode($result->getProposalBytes())
        );
        self::assertEquals(
            'MEQCIEfgYNT2Rve6kGy7Ter1/77KcJin1MImCroLqIzdiLmtAiBRjOCkd7aW6KM+qRzDxWmC1+X9aP/tzWD6/Z5a2E9zOA==',
            base64_encode($result->getSignature())
        );
    }

    /**
     * @dataProvider getProposalSignatureCharacterizationData
     * @param string $encodedProposalBytes
     * @param string $encodedSignature
     * @param string $proposalHeader
     * @param string $proposalPayload
     * @param string $proposalExtension
     */
    public function testGetSCharacterization(
        string $encodedProposalBytes,
        string $encodedSignature,
        string $proposalHeader,
        string $proposalPayload,
        string $proposalExtension
    ) {
        $proposal = new Proposal();
        $proposal->setHeader(base64_decode($proposalHeader));
        $proposal->setPayload(base64_decode($proposalPayload));
        $proposal->setExtension(base64_decode($proposalExtension));
        $result = $this->sut->signProposal($proposal, new \SplFileObject($this->privateKey->url()));

        self::assertInstanceOf(SignedProposal::class, $result);
        self::assertEquals($encodedProposalBytes, base64_encode($result->getProposalBytes()));
        self::assertEquals($encodedSignature, base64_encode($result->getSignature()));
    }

    /**
     * @dataProvider getChainCodeProposalDataset
     * @param string $dateTime
     * @param string $proposalHeader
     * @param string $proposalPayload
     * @param string $proposalExtension
     */
    public function testCreateChaincodeProposal(
        string $dateTime,
        string $proposalHeader,
        string $proposalPayload,
        string $proposalExtension
    ) {
        $proposal = $this->createChaincodeProposal($dateTime, $this->privateKeyFile);

        self::assertEquals(base64_decode($proposalHeader), $proposal->getHeader());
        self::assertEquals(base64_decode($proposalPayload), $proposal->getPayload());
        self::assertEquals(base64_decode($proposalExtension), $proposal->getExtension());
    }

    /**
     * @covers       \AmericanExpress\HyperledgerFabricClient\Signatory\MdanterEccSignatory::getS
     * @dataProvider dataGetS
     * @param string $dateTime
     * @param string $encodedProposalBytes
     * @param string $encodedSignature
     */
    public function testGetS(string $dateTime, string $encodedProposalBytes, string $encodedSignature)
    {
        $proposal = $this->createChaincodeProposal($dateTime, $this->privateKeyFile);
        $result = $this->sut->signProposal($proposal, new \SplFileObject($this->privateKey->url()));

        self::assertInstanceOf(SignedProposal::class, $result);
        self::assertEquals($encodedProposalBytes, base64_encode($result->getProposalBytes()));
        self::assertEquals($encodedSignature, base64_encode($result->getSignature()));
    }

    public function getProposalSignatureCharacterizationData()
    {
        $data = $this->loadStaticData();

        return array_map(
            function ($value) {
                return array_intersect_key(
                    $value,
                    array_flip([
                        'encodedProposalBytes',
                        'encodedSignature',
                        'proposalHeader',
                        'proposalPayload',
                        'proposalExtension',
                    ])
                );
            },
            $data
        );
    }

    public function dataGetS()
    {
        return $this->loadStaticData();
    }
}
