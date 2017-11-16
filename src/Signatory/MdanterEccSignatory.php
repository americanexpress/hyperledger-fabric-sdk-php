<?php
declare(strict_types=1);

namespace AmericanExpress\HyperledgerFabricClient\Signatory;

use AmericanExpress\HyperledgerFabricClient\Exception\InvalidArgumentException;
use AmericanExpress\HyperledgerFabricClient\ProtoFactory\SignedProposalFactory;
use AmericanExpress\HyperledgerFabricClient\Serializer\BinaryStringSerializer;
use Assert\Assertion;
use Assert\AssertionFailedException;
use Hyperledger\Fabric\Protos\Peer\Proposal;
use Hyperledger\Fabric\Protos\Peer\SignedProposal;
use Mdanter\Ecc\Crypto\Key\PrivateKeyInterface;
use Mdanter\Ecc\Crypto\Signature\Signature;
use Mdanter\Ecc\EccFactory;
use Mdanter\Ecc\Crypto\Signature\Signer;
use Mdanter\Ecc\Math\GmpMath;
use Mdanter\Ecc\Random\RandomGeneratorFactory;
use Mdanter\Ecc\Serializer\PrivateKey\PemPrivateKeySerializer;
use Mdanter\Ecc\Serializer\PrivateKey\DerPrivateKeySerializer;
use Mdanter\Ecc\Serializer\Signature\DerSignatureSerializer;

class MdanterEccSignatory implements SignatoryInterface
{
    /**
     * @var string
     */
    private $hashAlgorithm;

    /**
     * @var BinaryStringSerializer
     */
    private $binaryStringSerializer;

    /**
     * @var \Mdanter\Ecc\Math\GmpMathInterface
     */
    private $adapter;

    /**
     * @var \Mdanter\Ecc\Primitives\GeneratorPoint
     */
    private $generator;

    /**
     * @var Signer
     */
    private $signer;

    /**
     * Utils constructor.
     * @param string $hashAlgorithm
     */
    public function __construct(string $hashAlgorithm = 'sha256')
    {
        try {
            Assertion::inArray($hashAlgorithm, hash_algos());
        } catch (AssertionFailedException $e) {
            throw InvalidArgumentException::fromException($e);
        }

        $this->hashAlgorithm = $hashAlgorithm;
        $this->binaryStringSerializer = new BinaryStringSerializer();
        $this->adapter = EccFactory::getAdapter();
        $this->generator = EccFactory::getNistCurves()->generator256();
        $this->signer = new Signer($this->adapter);
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
        $key = $pemSerializer->parse($privateKey);

        return $key;
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

        $hash = $this->signer->hashData($this->generator, $this->hashAlgorithm, $dataString);

        # Derandomized signatures are not necessary, but can reduce
        # the attack surface for a private key that is to be used often.
        $random = RandomGeneratorFactory::getHmacRandomGenerator($privateKey, $hash, $this->hashAlgorithm);

        $randomK = $random->generate($this->generator->getOrder());
        $signature = $this->signer->sign($privateKey, $hash, $randomK);
        $s = $signature->getS();
        $halfOrder = $this->adapter->rightShift($this->generator->getOrder(), 1);
        $math = new GmpMath();

        if ($math->cmp($s, $halfOrder) > 0) {
            $s = $this->adapter->sub($this->generator->getOrder(), $s);
        }

        $eccSignature = new Signature($signature->getR(), $s);
        $serializer = new DerSignatureSerializer();
        $serializedSig = $serializer->serialize($eccSignature);

        return $serializedSig;
    }
}
