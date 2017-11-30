<?php
declare(strict_types=1);

namespace AmericanExpress\HyperledgerFabricClient\Peer;

interface PeerCollectionInterface
{
    /**
     * @param PeerInterface[] ...$peers
     * @return void
     */
    public function addPeers(PeerInterface ...$peers): void;
}
