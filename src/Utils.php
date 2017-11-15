<?php
declare(strict_types=1);

namespace AmericanExpress\HyperledgerFabricClient;

use Grpc\ChannelCredentials;
use Hyperledger\Fabric\Protos\Peer\ChaincodeInput;
use Hyperledger\Fabric\Protos\Peer\ChaincodeInvocationSpec;
use Hyperledger\Fabric\Protos\Peer\ChaincodeSpec;
use Hyperledger\Fabric\Protos\Peer\EndorserClient;

class Utils
{
    /**
     * @var ClientConfigInterface
     */
    private $config;

    /**
     * @var EndorserClient[]
     */
    private $endorserClients = [];

    /**
     * Utils constructor.
     * @param ClientConfigInterface $config
     */
    public function __construct(ClientConfigInterface $config)
    {
        $this->config = $config;
    }

    /**
     * Function for getting random nonce value
     *  random number(nonce) which in turn used to generate txId.
     */
    public function getNonce(): string
    {
        return \random_bytes($this->config->getIn(['nonce-size']));
    }

    /**
     * @param string $proposalString
     * @return array
     * convert string to byte array
     */
    public function toByteArray(string $proposalString): array
    {
        return \unpack('c*', $proposalString);
    }

    /**
     * @param string $org
     * @param string $network
     * @param string $peer
     * @return EndorserClient
     * Read connection configuration.
     */
    public function fabricConnect(string $org, string $network = 'test-network', string $peer = 'peer1'): EndorserClient
    {
        $host = $this->config->getIn([$network, $org, $peer, 'requests'], null);

        if (!\array_key_exists($host, $this->endorserClients)) {
            $this->endorserClients[$host] = new EndorserClient($host, [
                'credentials' => ChannelCredentials::createInsecure(),
            ]);
        }

        return $this->endorserClients[$host];
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
