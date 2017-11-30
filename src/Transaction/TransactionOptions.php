<?php

/**
 * Copyright 2017 American Express Travel Related Services Company, Inc.
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 * http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express
 * or implied. See the License for the specific language governing
 * permissions and limitations under the License.
 */

declare(strict_types=1);

namespace AmericanExpress\HyperledgerFabricClient\Transaction;

use AmericanExpress\HyperledgerFabricClient\Options\AbstractOptions;
use AmericanExpress\HyperledgerFabricClient\Peer\PeerCollectionInterface;
use AmericanExpress\HyperledgerFabricClient\Peer\PeerInterface;

class TransactionOptions extends AbstractOptions implements PeerCollectionInterface
{
    /**
     * @var PeerInterface[]
     */
    private $peers = [];

    /**
     * @return PeerInterface[]
     */
    public function getPeers(): array
    {
        return $this->peers;
    }

    /**
     * @param PeerInterface[] $peers
     * @return void
     */
    public function setPeers(array $peers): void
    {
        $this->peers = [];
        $this->addPeers(...$peers);
    }

    /**
     * @param PeerInterface[] ...$peers
     * @return void
     */
    public function addPeers(PeerInterface ...$peers): void
    {
        $this->peers = array_merge($this->peers, $peers);
    }

    /**
     * @return bool
     */
    public function hasPeers(): bool
    {
        return count($this->peers) > 0;
    }

    /**
     * @param PeerInterface[] ...$peers
     * @return static
     */
    public function withPeers(PeerInterface ...$peers): TransactionOptions
    {
        $options = new static();
        $options->setPeers($peers);

        return $options;
    }
}
