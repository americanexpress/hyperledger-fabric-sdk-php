<?php
namespace AmericanExpress\FabricClient;

use Grpc\ChannelCredentials;
use Hyperledger\Fabric\Protos\Peer\ChaincodeInput;
use Hyperledger\Fabric\Protos\Peer\ChaincodeInvocationSpec;
use Hyperledger\Fabric\Protos\Peer\ChaincodeSpec;
use Hyperledger\Fabric\Protos\Peer\EndorserClient;

class Utils
{

    private static $config = null;

    /**
     * Function for getting random nounce value
     *  random number(nounce) which in turn used to generate txId.
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
        $hashing = new Hash();
        $array = $hashing->generateByteArray($proposalString);

        return $array;
    }

    /**
     * @param $org
     * @return EndorserClient
     * Read connection configuration.
     */
    public function fabricConnect($org)
    {
        self::$config = AppConf::getOrgConfig($org);
        $host = self::$config["peer1"]["requests"];
        $connect = new EndorserClient($host, [
            'credentials' => ChannelCredentials::createInsecure(),
        ]);

        return $connect;
    }

    /**
     * @param $args
     * @return ChaincodeInvocationSpec specify parameters of chaincode to be invoked during transaction.
     * specify parameters of chaincode to be invoked during transaction.
     * @internal param $chaincodeID
     */
    public function createChaincodeInvocationSpec($args)
    {
        $chaincodeInput = new ChaincodeInput();
        $chaincodeInput->setArgs($args);

        $chaincodeSpec = new ChaincodeSpec();
        $chaincodeSpec->setInput($chaincodeInput);
        $chaincodeInvocationSpec = new ChaincodeInvocationSpec();
        $chaincodeInvocationSpec->setChaincodeSpec($chaincodeSpec);

        return $chaincodeInvocationSpec;
    }

    /**
     * @param array $arr
     * @return string
     * convert array to binary string
     */
    public function proposalArrayToBinaryString(array $arr)
    {
        $str = "";
        foreach ($arr as $elm) {
            $str .= chr((int)$elm);
        }

        return $str;
    }
}
