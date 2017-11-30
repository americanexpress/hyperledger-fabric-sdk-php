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

namespace AmericanExpress\HyperledgerFabricClient\Peer;

use AmericanExpress\HyperledgerFabricClient\EndorserClient\EndorserClientManager;
use AmericanExpress\HyperledgerFabricClient\EndorserClient\EndorserClientManagerInterface;

final class PeerFactory implements PeerFactoryInterface
{
    /**
     * @var EndorserClientManagerInterface
     */
    private $endorserClients;

    /**
     * PeerFactory constructor.
     * @param EndorserClientManagerInterface $endorserClients
     */
    public function __construct(EndorserClientManagerInterface $endorserClients = null)
    {
        $this->endorserClients = $endorserClients ?: new EndorserClientManager();
    }

    /**
     * @param PeerOptionsInterface $options
     * @return PeerInterface
     */
    public function fromPeerOptions(PeerOptionsInterface $options): PeerInterface
    {
        $endorserClient = $this->endorserClients->get($options->getRequests());

        return new Peer($endorserClient);
    }

    /**
     * @param mixed[] $options
     * @return PeerInterface
     */
    public function fromArray(array $options): PeerInterface
    {
        return $this->fromPeerOptions(new PeerOptions($options));
    }
}
