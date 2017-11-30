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

    /**
     * @param PeerInterface[] $peers
     * @return void
     */
    public function setPeers(array $peers): void;

    /**
     * @return PeerInterface[]
     */
    public function getPeers(): array;
}
