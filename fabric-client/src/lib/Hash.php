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
     * @param string $string
     * @return array
     */
    public function generateByteArray(string $string): array
    {
        return unpack('c*', $string);
    }

    /**
     * @param Proposal $proposal
     * @param string $org
     * @return string
     */
    public function signByteString(Proposal $proposal, string $org): string
    {
        $config = AppConf::getOrgConfig($org);
        $proposalString = $proposal->serializeToString();
        $proposalArray = (new Utils())->toByteArray($proposalString);
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
        $keyData = file_get_contents(ROOTPATH . $privateKeyPath);
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
        $algorithm = 'sha256';

        $key = $privateKeyData;

        $dataString = (new Utils())->proposalArrayToBinaryString($dataArray);

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
