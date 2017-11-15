<?php
declare(strict_types=1);

namespace AmericanExpress\HyperledgerFabricClient;

use Hyperledger\Fabric\Protos\Peer\EndorserClient;

interface EndorserClientManagerInterface
{
    /**
     * @param string $org
     * @param string $network
     * @param string $peer
     * @return EndorserClient
     */
    public function get(
        string $org,
        string $network = 'test-network',
        string $peer = 'peer1'
    ): EndorserClient;
}