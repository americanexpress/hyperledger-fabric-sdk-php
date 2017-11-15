<?php
declare(strict_types=1);

namespace AmericanExpress\HyperledgerFabricClient;

use AmericanExpress\HyperledgerFabricClient\ProtoFactory\EndorserClientFactory;
use Hyperledger\Fabric\Protos\Peer\EndorserClient;

class EndorserClientManager implements EndorserClientManagerInterface
{
    /**
     * @var EndorserClient[]
     */
    private $instances = [];

    /**
     * @param string $host
     * @return EndorserClient
     */
    public function get(string $host): EndorserClient {
        if (!\array_key_exists($host, $this->instances)) {
            $this->instances[$host] = EndorserClientFactory::fromInsecureChannelCredentials($host);
        }

        return $this->instances[$host];
    }
}
