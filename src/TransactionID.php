<?php
declare(strict_types=1);

namespace AmericanExpress\HyperledgerFabricClient;

use AmericanExpress\HyperledgerFabricClient\MSP\Identity;

class TransactionID
{
    /**
     * @var ClientConfigInterface
     */
    private $config;

    /**
     * @var Identity
     */
    private $identity;

    /**
     * @var Hash
     */
    private $hash;

    /**
     * Utils constructor.
     * @param ClientConfigInterface $config
     * @param Identity $identity
     * @param Hash $hash
     */
    public function __construct(ClientConfigInterface $config, Identity $identity, Hash $hash)
    {
        $this->config = $config;
        $this->identity = $identity;
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

        $identity = $this->identity->createSerializedIdentity($config['admin_certs'], $config['mspid']);
        $identityString = $identity->serializeToString();

        $noArray = $this->hash->toByteArray($nonce);
        $identityArray = $this->hash->toByteArray($identityString);
        $comp = array_merge($noArray, $identityArray);
        $compString = $this->hash->proposalArrayToBinaryString($comp);
        $txID = hash($this->config->getIn(['crypto-hash-algo']), $compString);

        return $txID;
    }
}
