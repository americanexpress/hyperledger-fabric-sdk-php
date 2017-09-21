<?php

namespace fabric\sdk;

class Utils
{

    static $config = null;

    function __construct()
    {

    }


    public static function addConfigFile($path)
    {

    }

    /**
     * Function for getting random nounce value
     * @return
     */
    public static function getNonce()
    {
        $random = random_bytes(24); // read 24 from sdk default.json
        return $random;
    }

    public function toByteArray($proposalString)
    {
        $hashing = new \Hash();
        $array = $hashing->generateByteArray($proposalString);
        return $array;
    }

    public function arrayToBinaryString(array $arr)
    {
        $str = "";
        foreach ($arr as $elm) {
            $str .= chr((int)$elm);
        }
        return $str;
    }

    function FabricConnect($org)
    {

        self::$config = \Config::getOrgConfig($org);

        $host = self::$config["peer1"]["requests"];

        $connect = new \Protos\EndorserClient($host, [
            'credentials' => \Grpc\ChannelCredentials::createInsecure(),
        ]);

        return $connect;
    }

    public function createChaincodeInvocationSpec($chaincodeID, $ccType)
    {
        $chaincodeInput = new \Protos\ChaincodeInput();

        $args = array();
        $args[] = "invoke";
        $args[] = "query";
        $args[] = "a";

        $chaincodeInput->setArgs($args);

        $chaincodeSpec = new \Protos\ChaincodeSpec();
        $chaincodeSpec->setType("1");
        $chaincodeSpec->setChaincodeId($chaincodeID);
        $chaincodeSpec->setInput($chaincodeInput);

        $chaincodeInvocationSpec = new \Protos\ChaincodeInvocationSpec();
        $chaincodeInvocationSpec->setChaincodeSpec($chaincodeSpec);

        return $chaincodeInvocationSpec;
    }


    public function proposalArrayToBinaryString(Array $arr)
    {
        $str = "";
        foreach ($arr as $elm) {
            $str .= chr((int)$elm);
        }
        return $str;
    }
}