<?php

use Mdanter\Ecc\EccFactory;
use Mdanter\Ecc\Crypto\Signature\Signer;
use Mdanter\Ecc\Serializer\PrivateKey\PemPrivateKeySerializer;
use Mdanter\Ecc\Serializer\PrivateKey\DerPrivateKeySerializer;
use Mdanter\Ecc\Serializer\Signature\DerSignatureSerializer;

class Hash
{

    function generateByteArray($string)
    {
        $bytearray = unpack('c*', $string);
        return $bytearray;
    }

    function signByteString(\Protos\Proposal $proposal)
    {
        $proposalString = $proposal->serializeToString();
        $proposalArray = (new fabric\sdk\Utils())->toByteArray($proposalString);
        $privateKeydata = $this->readPrivateKey(Config::getConfig("private_key_path"));
        $signData = $this->signData($privateKeydata, $proposalArray);
        return $signData;
    }

    function readPrivateKey($privateKeyPath)
    {

        $adapter = EccFactory::getAdapter();

        ## You'll be restoring from a key, as opposed to generating one.
        $keyData = file_get_contents($privateKeyPath);

        openssl_pkey_export($keyData, $privateKey);
        $pemSerializer = new PemPrivateKeySerializer(new DerPrivateKeySerializer($adapter));
        $key = $pemSerializer->parse($privateKey);

        return $key;
    }

    function signData($privateKeyData, $dataArray)
    {
        $adapter = EccFactory::getAdapter();
        $generator = EccFactory::getSecgCurves()->generator256k1();
        $useDerandomizedSignatures = true;
        $algorithm = 'sha256';


        $key = $privateKeyData;

        $dataString = (new fabric\sdk\Utils())->proposalArrayToBinaryString($dataArray);

        $signer = new Signer($adapter);
        $hash = $signer->hashData($generator, $algorithm, $dataString);

        # Derandomized signatures are not necessary, but can reduce
        # the attack surface for a private key that is to be used often.
        if ($useDerandomizedSignatures) {
            $random = \Mdanter\Ecc\Random\RandomGeneratorFactory::getHmacRandomGenerator($key, $hash, $algorithm);
        } else {
            $random = \Mdanter\Ecc\Random\RandomGeneratorFactory::getRandomGenerator();
        }

        $randomK = $random->generate($generator->getOrder());
        $signature = $signer->sign($key, $hash, $randomK);

        $serializer = new DerSignatureSerializer();
        $serializedSig = $serializer->serialize($signature);
        return $serializedSig;
    }
}