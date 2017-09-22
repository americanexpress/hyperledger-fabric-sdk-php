<?php

use Mdanter\Ecc\EccFactory;
use Mdanter\Ecc\Crypto\Signature\Signer;
use Mdanter\Ecc\Serializer\PrivateKey\PemPrivateKeySerializer;
use Mdanter\Ecc\Serializer\PrivateKey\DerPrivateKeySerializer;
use Mdanter\Ecc\Serializer\Signature\DerSignatureSerializer;

class Hash
{
    static $config = null;

    /**
     * @param $string
     * @return string
     */
    function generateBytes($string)
    {
        if (strlen($string) > 0) {
            $bytearray = array();
            foreach (str_split($string) as $i => $char) {
                $bytearray[] = $this->ordutf8($char);
            }
            $bytestring = implode("", $bytearray);
            return $bytestring;
        }
    }

    /**
     * @param $string
     * @return int
     */
    function ordutf8($string)
    {
        $offset = 0;
        $code = ord(substr($string, $offset, 1));
        if ($code >= 128) {        //otherwise 0xxxxxxx
            if ($code < 224) {
                $bytesnumber = 2;                //110xxxxx
            } elseif ($code < 240) {
                $bytesnumber = 3;        //1110xxxx
            } elseif ($code < 248) {
                $bytesnumber = 4;    //11110xxx
            }
            $codetemp = $code - 192 - ($bytesnumber > 2 ? 32 : 0) - ($bytesnumber > 3 ? 16 : 0);
            for ($i = 2; $i <= $bytesnumber; $i++) {
                $offset++;
                $code2 = ord(substr($string, $offset, 1)) - 128;        //10xxxxxx
                $codetemp = $codetemp * 64 + $code2;
            }
            $code = $codetemp;
        }
        $offset += 1;
        if ($offset >= strlen($string)) {
            $offset = -1;
        }
        return $code;
    }

    /**
     * @param $string
     * @return array
     */
    function generateByteArray($string)
    {
        $bytearray = unpack('c*', $string);
        return $bytearray;
    }

    function signByteString(\Protos\Proposal $proposal, $org)
    {
        self::$config =  \Config::getOrgConfig($org);
        $proposalString = $proposal->serializeToString();
        $proposalArray = (new fabric\sdk\Utils())->toByteArray($proposalString);
        $privateKeydata = $this->readPrivateKey(self::$config["private_key"]);
        $signData = $this->signData($privateKeydata, $proposalArray);
        return $signData;
    }

    /**
     * @param $privateKeyPath
     * @return \Mdanter\Ecc\Crypto\Key\PrivateKey|\Mdanter\Ecc\Crypto\Key\PrivateKeyInterface
     *
     */
    function readPrivateKey($privateKeyPath)
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
     * @param $privateKeyData
     * @param $dataArray
     * @return string
     * sign private key of node
     */
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