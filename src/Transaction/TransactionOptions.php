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
use AmericanExpress\HyperledgerFabricClient\Peer\PeerOptionsInterface;

class TransactionOptions extends AbstractOptions
{
    /**
     * @var PeerOptionsInterface|null
     */
    private $peer;

    /**
     * @return PeerOptionsInterface|null
     */
    public function getPeer(): ?PeerOptionsInterface
    {
        return $this->peer;
    }

    /**
     * @param PeerOptionsInterface $peer
     * @return void
     */
    public function setPeer(PeerOptionsInterface $peer): void
    {
        $this->peer = $peer;
    }

    /**
     * @return bool
     */
    public function hasPeer(): bool
    {
        return (bool) $this->peer;
    }
}
