<?php
declare(strict_types=1);

namespace AmericanExpress\HyperledgerFabricClient;

use AmericanExpress\HyperledgerFabricClient\MSP\Identity;

class TransactionID
{
    /**
     * @var Utils
     */
    private $utils;

    /**
     * @var ClientConfigInterface
     */
    private $config;

    /**
     * @var Identity
     */
    private $identity;

    /**
     * Utils constructor.
     * @param ClientConfigInterface $config
     */
    public function __construct(ClientConfigInterface $config)
    {
        $this->config = $config;
        $this->identity = new Identity();
        $this->utils = new Utils($config);
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

        $utils = $this->utils;
        $noArray = $utils->toByteArray($nonce);
        $identityArray = $utils->toByteArray($identityString);
        $comp = array_merge($noArray, $identityArray);
        $compString = $utils->proposalArrayToBinaryString($comp);
        $txID = hash($this->config->getDefault('crypto-hash-algo'), $compString);

        return $txID;
    }
}
