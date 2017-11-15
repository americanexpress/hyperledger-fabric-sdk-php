<?php
declare(strict_types=1);

namespace AmericanExpress\HyperledgerFabricClient\Cryptography;

use AmericanExpress\HyperledgerFabricClient\Exception\InvalidArgumentException;
use Assert\Assertion;
use Assert\AssertionFailedException;
use Hyperledger\Fabric\Protos\MSP\SerializedIdentity;
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

class MdanterEcc implements CryptographyInterface
{
    /**
     * @var int
     */
    private $nonceSize;

    /**
     * @var string
     */
    private $hashAlgorithm;

    /**
     * Utils constructor.
     * @param int $nonceSize
     * @param string $hashAlgorithm
     */
    public function __construct(int $nonceSize = 24, string $hashAlgorithm = 'sha256')
    {
        try {
            Assertion::greaterThan($nonceSize, -1);
            Assertion::inArray($hashAlgorithm, hash_algos());
        } catch (AssertionFailedException $e) {
            throw InvalidArgumentException::fromException($e);
        }

        $this->nonceSize = $nonceSize;
        $this->hashAlgorithm = $hashAlgorithm;
    }

    /**
     * @param Proposal $proposal
     * @param \SplFileObject $privateKey
     * @return string
     */
    public function signByteString(Proposal $proposal, \SplFileObject $privateKey): string
    {
        $proposalString = $proposal->serializeToString();
        $proposalArray = $this->toByteArray($proposalString);
        $privateKey = $this->readPrivateKey($privateKey);
        $signData = $this->signData($privateKey, $proposalArray);

        return $signData;
    }

    /**
     * @param \SplFileObject $privateKeyPath
     * @return PrivateKeyInterface
     *
     */
    private function readPrivateKey(\SplFileObject $privateKeyPath): PrivateKeyInterface
    {
        $adapter = EccFactory::getAdapter();

        ## You'll be restoring from a key, as opposed to generating one.
        $keyData = $privateKeyPath->fread($privateKeyPath->getSize());
        \openssl_pkey_export($keyData, $privateKey);
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

        $key = $privateKeyData;

        $dataString = $this->arrayToBinaryString($dataArray);

        $signer = new Signer($adapter);
        $hash = $signer->hashData($generator, $this->hashAlgorithm, $dataString);

        # Derandomized signatures are not necessary, but can reduce
        # the attack surface for a private key that is to be used often.
        $random = RandomGeneratorFactory::getHmacRandomGenerator($key, $hash, $this->hashAlgorithm);

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

    /**
     * Function for getting random nonce value
     *  random number(nonce) which in turn used to generate txId.
     */
    public function getNonce(): string
    {
        return \random_bytes($this->nonceSize);
    }

    /**
     * @param string $proposalString
     * @return array
     * convert string to byte array
     */
    private function toByteArray(string $proposalString): array
    {
        return \unpack('c*', $proposalString);
    }

    /**
     * @param array $arr
     * @return string
     * convert array to binary string
     */
    private function arrayToBinaryString(array $arr): string
    {
        $str = '';
        foreach ($arr as $elm) {
            $str .= \chr((int)$elm);
        }

        return $str;
    }

    /**
     * @param SerializedIdentity $serializedIdentity
     * @param string $nonce
     * @return string
     */
    public function createTxId(SerializedIdentity $serializedIdentity, string $nonce): string
    {
        $noArray = $this->toByteArray($nonce);

        $identityArray = $this->toByteArray($serializedIdentity->serializeToString());

        $comp = \array_merge($noArray, $identityArray);

        $compString = $this->arrayToBinaryString($comp);

        return \hash($this->hashAlgorithm, $compString);
    }
}
