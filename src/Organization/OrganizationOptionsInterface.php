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

namespace AmericanExpress\HyperledgerFabricClient\Organization;

use AmericanExpress\HyperledgerFabricClient\Peer\PeerOptions;
use AmericanExpress\HyperledgerFabricClient\Peer\PeerOptionsInterface;

interface OrganizationOptionsInterface
{
    /**
     * @return string|null
     */
    public function getName(): ?string;

    /**
     * @return string|null
     */
    public function getMspId(): ?string;

    /**
     * @return string[]
     */
    public function getCa(): array;

    /**
     * @return string|null
     */
    public function getAdminCerts(): ?string;

    /**
     * @return string|null
     */
    public function getPrivateKey(): ?string;

    /**
     * @return PeerOptions[]
     */
    public function getPeers(): array;

    /**
     * @param string $name
     * @return PeerOptionsInterface|null
     */
    public function getPeerByName(string $name): ?PeerOptionsInterface;

    /**
     * @return PeerOptionsInterface|null
     */
    public function getDefaultPeer(): ?PeerOptionsInterface;
}
