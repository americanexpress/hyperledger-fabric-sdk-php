<?php

use Mdanter\Ecc\EccFactory;
use Mdanter\Ecc\Crypto\Signature\Signer;
use Mdanter\Ecc\Serializer\PrivateKey\PemPrivateKeySerializer;
use Mdanter\Ecc\Serializer\PrivateKey\DerPrivateKeySerializer;
use Mdanter\Ecc\Serializer\Signature\DerSignatureSerializer;
use org\amex\fabric_client;

class Hash
{

    public $PRIVATE_KEY = "../../test/fixtures/resources/6b32e59640c594cf633ad8c64b5958ef7e5ba2a205cfeefd44a9e982ce624d93_sk";

    function ordutf8($string)
    {
        $offset=0;
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
                $offset ++;
                $code2 = ord(substr($string, $offset, 1)) - 128;        //10xxxxxx
                $codetemp = $codetemp*64 + $code2;
            }
            $code = $codetemp;
        }
        $offset += 1;
        if ($offset >= strlen($string)) {
            $offset = -1;
        }
        return $code;
    }

    function generateBytes($string)
    {
        if (strlen($string)>0) {
            $bytearray = array();
            foreach (str_split($string) as $i => $char) {
                //echo "There were $val instance(s) of \"" , $i , "\" in the string.\n";
                $bytearray[] = $this->ordutf8($char);
            }
            $bytestring = implode("", $bytearray);
            //print_r($bytearray);
            return $bytestring;
        }
    }

    function generateByteArray($string)
    {
        $bytearray = unpack('c*', $string);
        //print_r($bytearray);
        return $bytearray;
    }

    function readPrivateKey($privateKeyPath)
    {
    
        $adapter = EccFactory::getAdapter();
    
        ## You'll be restoring from a key, as opposed to generating one.
        $keyData = file_get_contents($privateKeyPath);
        /*$keyData = "-----BEGIN EC PRIVATE KEY-----
        MHcCAQEEIIZwO63YG2Yv8J3brIl48n9CoBOjimY0PuSDY8HpUQAuoAoGCCqGSM49
        AwEHoUQDQgAEm97voWPlPxWb4WAzendPBydb+elCVMs/59jR8iYE4OpAtOQGiTHh
        iIpxBdCXibUQzVUBs6ECUGI581E0vRbxoQ==
        -----END EC PRIVATE KEY-----";*/
        $pemSerializer = new PemPrivateKeySerializer(new DerPrivateKeySerializer($adapter));
        $key = $pemSerializer->parse($keyData);
    
        return $key;
    }
        
    function signData($privateKeyData, $dataArray)
    {
        $adapter = EccFactory::getAdapter();
        $generator = EccFactory::getSecgCurves()->generator256k1();
        $useDerandomizedSignatures = true;
        $algorithm = 'sha256';
        
    
        $key = $privateKeyData;
        
        $dataString = proposalArrayToBinaryString($dataArray);
        
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
        //print_r($signature);die();
        
        $serializer = new DerSignatureSerializer();
        $serializedSig = $serializer->serialize($signature);
        return $serializedSig;
    //    return base64_encode($serializedSig) . PHP_EOL;
    //    return base64_encode($serializedSig) . PHP_EOL;
        //die();
    }


    function signByteString(\Protos\Proposal $proposal)
    {
        $proposalString = $proposal->serializeToString();
        $proposalArray = (new fabric_client\Utils())->toByteArray($proposalString);
        $privateKeyPath = $this->PRIVATE_KEY;
        $privateKeydata = $this->readPrivateKey($privateKeyPath);
        $signData = $this->signData($privateKeydata, $proposalArray);
        return $signData;
    }
}