<?php

namespace fabric\sdk;

class Utils
{

    static $config = null;

    /**
     * Function for getting random nounce value
     * @return random number(nounce) which in turn used to generate txId.
     */
    public static function getNonce()
    {
        $random = random_bytes(24); // read 24 from sdk default.json
        return $random;
    }

    /**
     * @param $proposalString
     * @return array
     * convert string to byte array
     */
    public function toByteArray($proposalString)
    {
        $hashing = new \Hash();
        $array = $hashing->generateByteArray($proposalString);
        return $array;
    }

    /**
     * @param $org
     * @return \Protos\EndorserClient
     * Read connection configuration.
     */
    function FabricConnect($org)
    {
        self::$config = \Config::getOrgConfig($org);
        $host = self::$config["peer1"]["requests"];
        $connect = new \Protos\EndorserClient($host, [
            'credentials' => \Grpc\ChannelCredentials::createInsecure(),
        ]);

        return $connect;
    }

    /**
     * @param $chaincodeID
     * @param $args
     * @return \Protos\ChaincodeInvocationSpec
     * specify parameters of chaincode to be invoked during transaction.
     */
    public function createChaincodeInvocationSpec($args)
    {
        $chaincodeInput = new \Protos\ChaincodeInput();

        $chaincodeInput->setArgs($args);

        $chaincodeSpec = new \Protos\ChaincodeSpec();
        $chaincodeSpec->setType("1");
        $chaincodeSpec->setInput($chaincodeInput);
        $chaincodeInvocationSpec = new \Protos\ChaincodeInvocationSpec();
        $chaincodeInvocationSpec->setChaincodeSpec($chaincodeSpec);

        return $chaincodeInvocationSpec;
    }

    /**
     * @param array $arr
     * @return string
     * convert array to binary string
     */
    public function proposalArrayToBinaryString(Array $arr)
    {
        $str = "";
        foreach ($arr as $elm) {
            $str .= chr((int)$elm);
        }
        return $str;
    }
}