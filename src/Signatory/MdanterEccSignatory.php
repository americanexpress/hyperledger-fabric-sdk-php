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

namespace AmericanExpress\HyperledgerFabricClient\Signatory;

use AmericanExpress\HyperledgerFabricClient\Exception\RuntimeException;
use AmericanExpress\HyperledgerFabricClient\ProtoFactory\SignedProposalFactory;
use AmericanExpress\HyperledgerFabricClient\Serializer\BinaryStringSerializer;
use AmericanExpress\HyperledgerFabricClient\HashAlgorithm;
use Hyperledger\Fabric\Protos\Peer\Proposal;
use Hyperledger\Fabric\Protos\Peer\SignedProposal;
use Mdanter\Ecc\Crypto\Key\PrivateKeyInterface;
use Mdanter\Ecc\Crypto\Signature\Signature;
use Mdanter\Ecc\EccFactory;
use Mdanter\Ecc\Crypto\Signature\Signer;
use Mdanter\Ecc\Math\GmpMath;
use Mdanter\Ecc\Math\GmpMathInterface;
use Mdanter\Ecc\Primitives\GeneratorPoint;
use Mdanter\Ecc\Random\RandomGeneratorFactory;
use Mdanter\Ecc\Serializer\PrivateKey\PemPrivateKeySerializer;
use Mdanter\Ecc\Serializer\PrivateKey\DerPrivateKeySerializer;
use Mdanter\Ecc\Serializer\Signature\DerSignatureSerializer;

final class MdanterEccSignatory implements SignatoryInterface
{
    /**
     * @var DerSignatureSerializer
     */
    private $derSignatureSerializer;

    /**
     * @var GmpMath
     */
    private $gmpMath;

    /**
     * @var HashAlgorithm
     */
    private $hashAlgorithm;

    /**
     * @var BinaryStringSerializer
     */
    private $binaryStringSerializer;

    /**
     * @var GmpMathInterface
     */
    private $adapter;

    /**
     * @var GeneratorPoint
     */
    private $generator;

    /**
     * @var Signer
     */
    private $signer;

    /**
     * Utils constructor.
     * @param HashAlgorithm $hashAlgorithm
     * @throws RuntimeException
     * @throws \AmericanExpress\HyperledgerFabricClient\Exception\InvalidArgumentException
     */
    public function __construct(HashAlgorithm $hashAlgorithm = null)
    {
        $this->hashAlgorithm = $hashAlgorithm ?: new HashAlgorithm();
        $this->binaryStringSerializer = new BinaryStringSerializer();
        $this->adapter = EccFactory::getAdapter();
        $this->generator = EccFactory::getNistCurves()->generator256();
        $this->signer = new Signer($this->adapter);
        $this->gmpMath = new GmpMath();
        $this->derSignatureSerializer = new DerSignatureSerializer();
    }

    /**
     * @param Proposal $proposal
     * @param \SplFileObject $privateKeyFile
     * @return SignedProposal
     */
    public function signProposal(Proposal $proposal, \SplFileObject $privateKeyFile): SignedProposal
    {
        $proposalString = $proposal->serializeToString();
        $proposalArray = $this->binaryStringSerializer->deserialize($proposalString);
        $privateKey = $this->readPrivateKey($privateKeyFile);
        $signature = $this->signData($privateKey, $proposalArray);

        return SignedProposalFactory::fromProposal($proposal, $signature);
    }

    /**
     * @param \SplFileObject $privateKeyPath
     * @return PrivateKeyInterface
     *
     */
    private function readPrivateKey(\SplFileObject $privateKeyPath): PrivateKeyInterface
    {
        ## You'll be restoring from a key, as opposed to generating one.
        $keyData = $privateKeyPath->fread($privateKeyPath->getSize());
        \openssl_pkey_export($keyData, $privateKey);
        $pemSerializer = new PemPrivateKeySerializer(new DerPrivateKeySerializer($this->adapter));

        return $pemSerializer->parse($privateKey);
    }

    /**
     * @param PrivateKeyInterface $privateKey
     * @param $dataArray
     * @return string
     * sign private key of node
     */
    private function signData(PrivateKeyInterface $privateKey, array $dataArray): string
    {
        $dataString = $this->binaryStringSerializer->serialize($dataArray);

        $hash = $this->signer->hashData($this->generator, (string) $this->hashAlgorithm, $dataString);

        # Derandomized signatures are not necessary, but can reduce
        # the attack surface for a private key that is to be used often.
        $random = RandomGeneratorFactory::getHmacRandomGenerator($privateKey, $hash, (string) $this->hashAlgorithm);

        $randomK = $random->generate($this->generator->getOrder());

        $signature = $this->signer->sign($privateKey, $hash, $randomK);

        $eccSignature = new Signature($signature->getR(), $this->getS($signature));

        return $this->derSignatureSerializer->serialize($eccSignature);
    }

    /**
     * @param Signature $signature
     * @return \GMP
     */
    private function getS(Signature $signature): \GMP
    {
        $order = $this->generator->getOrder();
        $halfOrder = $this->adapter->rightShift($order, 1);

        $s = $signature->getS();
        if ($this->gmpMath->cmp($s, $halfOrder) > 0) {
            $s = $this->adapter->sub($order, $s);
        }

        return $s;
    }
}
