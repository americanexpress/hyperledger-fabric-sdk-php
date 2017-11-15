<?php
declare(strict_types=1);

namespace AmericanExpress\HyperledgerFabricClient;

use AmericanExpress\HyperledgerFabricClient\Factory\EndorserClientFactory;
use Hyperledger\Fabric\Protos\Peer\EndorserClient;

class EndorserClientManager implements EndorserClientManagerInterface
{
    /**
     * @var ClientConfigInterface
     */
    private $config;

    /**
     * @var EndorserClient[]
     */
    private $instances = [];

    /**
     * EndorserClientManager constructor.
     * @param ClientConfigInterface $config
     */
    public function __construct(ClientConfigInterface $config)
    {
        $this->config = $config;
    }

    /**
     * Read connection configuration.
     * @param string $org
     * @param string $network
     * @param string $peer
     * @return EndorserClient
     */
    public function get(
        string $org,
        string $network = 'test-network',
        string $peer = 'peer1'
    ): EndorserClient {
        $host = $this->config->getIn([$network, $org, $peer, 'requests'], null);

        if (!\array_key_exists($host, $this->instances)) {
            $this->instances[$host] = EndorserClientFactory::fromInsecureChannelCredentials($host);
        }

        return $this->instances[$host];
    }
}
