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

use AmericanExpress\HyperledgerFabricClient\Exception\UnexpectedValueException;
use AmericanExpress\HyperledgerFabricClient\Options\AbstractOptions;
use AmericanExpress\HyperledgerFabricClient\Peer\PeerOptions;
use AmericanExpress\HyperledgerFabricClient\Peer\PeerOptionsInterface;
use AmericanExpress\HyperledgerFabricClient\Transaction\TransactionRequest;

class OrganizationOptions extends AbstractOptions implements OrganizationOptionsInterface
{
    /**
     * @var string|null
     */
    private $name;

    /**
     * @var string|null
     */
    private $mspId;

    /**
     * @var string[]
     */
    private $ca = [];

    /**
     * @var string|null
     */
    private $adminCerts;

    /**
     * @var string|null
     */
    private $privateKey;

    /**
     * @var PeerOptions[]
     */
    private $peers = [];

    /**
     * @return string|null
     */
    public function getName(): ?string
    {
        return $this->name;
    }

    /**
     * @param string $name
     * @return void
     */
    public function setName(string $name): void
    {
        $this->name = $name;
    }

    /**
     * @return string|null
     */
    public function getMspId(): ?string
    {
        return $this->mspId;
    }

    /**
     * @param string $mspId
     * @return void
     */
    public function setMspId(string $mspId): void
    {
        $this->mspId = $mspId;
    }

    /**
     * @return string[]
     */
    public function getCa(): array
    {
        return $this->ca;
    }

    /**
     * @param string[] $ca
     * @return void
     */
    public function setCa(array $ca): void
    {
        $this->ca = $ca;
    }

    /**
     * @return string|null
     */
    public function getAdminCerts(): ?string
    {
        return $this->adminCerts;
    }

    /**
     * @param string $adminCerts
     * @return void
     */
    public function setAdminCerts(string $adminCerts): void
    {
        $this->adminCerts = $adminCerts;
    }

    /**
     * @return string|null
     */
    public function getPrivateKey(): ?string
    {
        return $this->privateKey;
    }

    /**
     * @param string $privateKey
     * @return void
     */
    public function setPrivateKey(string $privateKey): void
    {
        $this->privateKey = $privateKey;
    }

    /**
     * @return PeerOptions[]
     */
    public function getPeers(): array
    {
        return $this->peers;
    }

    /**
     * @param array|PeerOptions[] $peers
     * @return void
     * @throws \AmericanExpress\HyperledgerFabricClient\Exception\BadMethodCallException
     */
    public function setPeers(array $peers): void
    {
        $this->peers = array_map(function ($peer): PeerOptionsInterface {
            return $peer instanceof PeerOptionsInterface ? $peer : new PeerOptions($peer);
        }, $peers);
    }

    /**
     * @param string $name
     * @return PeerOptionsInterface|null
     */
    public function getPeerByName(string $name): ?PeerOptionsInterface
    {
        $peers = array_filter($this->peers, function (PeerOptionsInterface $peer) use ($name): bool {
            return $peer->getName() === $name;
        });

        return count($peers) > 0 ? reset($peers) : null;
    }

    /**
     * @param TransactionRequest|null $context
     * @return PeerOptionsInterface
     * @throws UnexpectedValueException
     */
    public function getPeerByTransactionRequest(TransactionRequest $context = null): PeerOptionsInterface
    {
        if (count($this->peers) < 1) {
            throw new UnexpectedValueException(sprintf(
                'Organization `%s` has no peers.',
                $this->name
            ));
        }

        $peerName = $context ? $context->getPeer() : null;

        if (!$peerName) {
            return $this->getDefaultPeer();
        }

        $peer = $this->getPeerByName($peerName);

        if ($peer) {
            return $peer;
        }

        throw new UnexpectedValueException(sprintf(
            'Peer `%s` is invalid for organization `%s`',
            $context->getPeer(),
            $this->name
        ));
    }

    /**
     * @return PeerOptionsInterface|null
     */
    public function getDefaultPeer(): ?PeerOptionsInterface
    {
        return \reset($this->peers);
    }
}
