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
use AmericanExpress\HyperledgerFabricClient\Peer\PeerOptions;
use AmericanExpress\HyperledgerFabricClient\Peer\PeerOptionsInterface;

class TransactionOptions extends AbstractOptions
{
    /**
     * @var PeerOptionsInterface[]
     */
    private $peers = [];

    /**
     * @return PeerOptionsInterface[]
     */
    public function getPeers(): array
    {
        return $this->peers;
    }

    /**
     * @param PeerOptionsInterface[] $peers
     * @return void
     */
    public function setPeers(array $peers): void
    {
        $peers = array_map(function ($peer): PeerOptionsInterface {
            return $peer instanceof PeerOptionsInterface ? $peer : new PeerOptions($peer);
        }, $peers);

        $this->peers = [];
        $this->addPeers(...$peers);
    }

    /**
     * @param PeerOptionsInterface[] ...$peers
     */
    public function addPeers(PeerOptionsInterface ...$peers): void
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
}
