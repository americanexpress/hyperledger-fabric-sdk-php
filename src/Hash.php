<?php
declare(strict_types=1);

namespace AmericanExpress\HyperledgerFabricClient;

use Hyperledger\Fabric\Protos\Peer\Proposal;
use Mdanter\Ecc\Crypto\Key\PrivateKeyInterface;
use Mdanter\Ecc\Crypto\Signature\Signature;
use Mdanter\Ecc\EccFactory;
use Mdanter\Ecc\Crypto\Signature\Signer;
use Mdanter\Ecc\Math\GmpMath;
use Mdanter\Ecc\Random\RandomGeneratorFactory;
use Mdanter\Ecc\Serializer\PrivateKey\PemPrivateKeySerializer;
use Mdanter\Ecc\Serializer\PrivateKey\DerPrivateKeySerializer;
use Mdanter\Ecc\Serializer\Signature\DerSignatureSerializer;

class Hash
{
    /**
     * @var Utils
     */
    private $utils;

    /**
     * @var ClientConfigInterface
     */
    private $config;

    /**
     * Utils constructor.
     * @param ClientConfigInterface $config
     * @param Utils $utils
     */
    public function __construct(ClientConfigInterface $config, Utils $utils)
    {
        $this->config = $config;
        $this->utils = $utils;
    }

    /**
     * @param Proposal $proposal
     * @param string $org
     * @param string $network
     * @return string
     */
    public function signByteString(Proposal $proposal, string $org, string $network = 'test-network'): string
    {
        $config = $this->config->getIn([$network, $org], null);
        $proposalString = $proposal->serializeToString();
        $proposalArray = $this->utils->toByteArray($proposalString);
        $privateKey = $this->readPrivateKey($config["private_key"]);
        $signData = $this->signData($privateKey, $proposalArray);

        return $signData;
    }

    /**
     * @param string $privateKeyPath
     * @return PrivateKeyInterface
     *
     */
    private function readPrivateKey(string $privateKeyPath): PrivateKeyInterface
    {

        $adapter = EccFactory::getAdapter();

        ## You'll be restoring from a key, as opposed to generating one.
        $keyData = \file_get_contents($privateKeyPath);
        openssl_pkey_export($keyData, $privateKey);
        $pemSerializer = new PemPrivateKeySerializer(new DerPrivateKeySerializer($adapter));
        $key = $pemSerializer->parse($privateKey);

        return $key;
    }

    /**
     * @param PrivateKeyInterface $privateKeyData
     * @param $dataArray
     * @return string
     * sign private key of node
     */
    private function signData(PrivateKeyInterface $privateKeyData, array $dataArray)
    {
        $adapter = EccFactory::getAdapter();
        $generator = EccFactory::getNistCurves()->generator256();
        $algorithm = $this->config->getDefault('crypto-hash-algo');

        $key = $privateKeyData;

        $dataString = $this->utils->proposalArrayToBinaryString($dataArray);

        $signer = new Signer($adapter);
        $hash = $signer->hashData($generator, $algorithm, $dataString);

        # Derandomized signatures are not necessary, but can reduce
        # the attack surface for a private key that is to be used often.
        $random = RandomGeneratorFactory::getHmacRandomGenerator($key, $hash, $algorithm);

        $randomK = $random->generate($generator->getOrder());
        $signature = $signer->sign($key, $hash, $randomK);
        $s = $signature->getS();
        $halfOrder = $adapter->rightShift($generator->getOrder(), 1);
        $math = new GmpMath();

        if ($math->cmp($s, $halfOrder) > 0) {
            $s = $adapter->sub($generator->getOrder(), $s);
        }

        $eccSignature = new Signature($signature->getR(), $s);
        $serializer = new DerSignatureSerializer();
        $serializedSig = $serializer->serialize($eccSignature);

        return $serializedSig;
    }
}
