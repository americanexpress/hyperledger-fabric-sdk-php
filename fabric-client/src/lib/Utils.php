<?php
declare(strict_types=1);

namespace AmericanExpress\FabricClient;

use Grpc\ChannelCredentials;
use Hyperledger\Fabric\Protos\Peer\ChaincodeInput;
use Hyperledger\Fabric\Protos\Peer\ChaincodeInvocationSpec;
use Hyperledger\Fabric\Protos\Peer\ChaincodeSpec;
use Hyperledger\Fabric\Protos\Peer\EndorserClient;

class Utils
{
    /**
     * Function for getting random nonce value
     *  random number(nonce) which in turn used to generate txId.
     */
    public function getNonce(): string
    {
        $random = random_bytes(24); // read 24 from sdk default.json

        return $random;
    }

    /**
     * @param string $proposalString
     * @return array
     * convert string to byte array
     */
    public function toByteArray(string $proposalString): array
    {
        $hashing = new Hash();
        $array = $hashing->generateByteArray($proposalString);

        return $array;
    }

    /**
     * @param string $org
     * @return EndorserClient
     * Read connection configuration.
     */
    public function fabricConnect(string $org): EndorserClient
    {
        $config = AppConf::getOrgConfig($org);
        $host = $config["peer1"]["requests"];
        $connect = new EndorserClient($host, [
            'credentials' => ChannelCredentials::createInsecure(),
        ]);

        return $connect;
    }

    /**
     * @param string[]|\Google\Protobuf\Internal\RepeatedField $args
     * @return ChaincodeInvocationSpec specify parameters of chaincode to be invoked during transaction.
     * specify parameters of chaincode to be invoked during transaction.
     */
    public function createChaincodeInvocationSpec($args): ChaincodeInvocationSpec
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
    public function proposalArrayToBinaryString(array $arr): string
    {
        $str = "";
        foreach ($arr as $elm) {
            $str .= chr((int)$elm);
        }

        return $str;
    }
}
