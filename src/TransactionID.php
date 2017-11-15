<?php
declare(strict_types=1);

namespace AmericanExpress\HyperledgerFabricClient;

use AmericanExpress\HyperledgerFabricClient\Factory\SerializedIdentityFactory;
use Hyperledger\Fabric\Protos\MSP\SerializedIdentity;
use function igorw\get_in;

class TransactionID
{
    /**
     * @var ClientConfigInterface
     */
    private $config;

    /**
     * @var Hash
     */
    private $hash;

    /**
     * Utils constructor.
     * @param ClientConfigInterface $config
     * @param Hash $hash
     */
    public function __construct(ClientConfigInterface $config, Hash $hash)
    {
        $this->config = $config;
        $this->hash = $hash;
    }

    /**
     * @param string $nonce
     * @param string $org
     * @param string $network
     * @return string
     * Generate transaction Id using Ecert of member.
     */
    public function getTxId(string $nonce, string $org, string $network = 'test-network'): string
    {
        $config = $this->config->getIn([$network, $org], null);

        $identity = SerializedIdentityFactory::fromFile(
            (string) get_in($config, ['mspid']),
            new \SplFileObject(get_in($config, ['admin_certs']))
        );

        return self::fromSerializedIdentity($identity, $nonce);
    }

    /**
     * @param SerializedIdentity $serializedIdentity
     * @param string $nonce
     * @return string
     */
    public function fromSerializedIdentity(SerializedIdentity $serializedIdentity, string $nonce): string
    {
        $identityString = $serializedIdentity->serializeToString();

        $noArray = $this->hash->toByteArray($nonce);
        $identityArray = $this->hash->toByteArray($identityString);
        $comp = \array_merge($noArray, $identityArray);
        $compString = $this->hash->proposalArrayToBinaryString($comp);
        $txID = \hash($this->config->getIn(['crypto-hash-algo']), $compString);

        return $txID;
    }
}
