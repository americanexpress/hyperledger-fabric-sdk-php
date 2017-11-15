<?php
declare(strict_types=1);

namespace AmericanExpress\HyperledgerFabricClient;

use Hyperledger\Fabric\Protos\Peer\EndorserClient;

interface EndorserClientManagerInterface
{
    /**
     * @param string $host
     * @return EndorserClient
     */
    public function get(string $host): EndorserClient;
}
