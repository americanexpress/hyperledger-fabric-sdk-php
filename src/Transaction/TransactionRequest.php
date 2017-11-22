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
use AmericanExpress\HyperledgerFabricClient\Organization\OrganizationOptions;
use AmericanExpress\HyperledgerFabricClient\Organization\OrganizationOptionsInterface;
use AmericanExpress\HyperledgerFabricClient\Peer\PeerOptionsInterface;
use Hyperledger\Fabric\Protos\Peer\ChaincodeID;

class TransactionRequest extends AbstractOptions
{
    /**
     * @var OrganizationOptions
     */
    private $organization;

    /**
     * @var string|null
     */
    private $peer;

    /**
     * @var ChaincodeID|null
     */
    private $chaincodeId;

    /**
     * @var array
     */
    private $args = [];

    public function __construct($options = [])
    {
        $this->organization = new OrganizationOptions();

        parent::__construct($options);
    }

    /**
     * @return OrganizationOptionsInterface
     */
    public function getOrganization(): OrganizationOptionsInterface
    {
        return $this->organization;
    }

    /**
     * @param OrganizationOptionsInterface $organization
     * @return void
     */
    public function setOrganization(OrganizationOptionsInterface $organization): void
    {
        $this->organization = $organization;
    }

    /**
     * @return string|null
     */
    public function getPeer(): ?string
    {
        return $this->peer;
    }

    /**
     * @param string $peer
     * @return void
     */
    public function setPeer(string $peer): void
    {
        $this->peer = $peer;
    }

    /**
     * @return PeerOptionsInterface|null
     */
    public function getPeerOptions(): ?PeerOptionsInterface
    {
        return $this->peer ? $this->organization->getPeerByName($this->peer) : null;
    }

    /**
     * @return ChaincodeID|null
     */
    public function getChaincodeId(): ?ChaincodeID
    {
        return $this->chaincodeId;
    }

    /**
     * @param ChaincodeID $chaincodeId
     * @return void
     */
    public function setChaincodeId(ChaincodeID $chaincodeId): void
    {
        $this->chaincodeId = $chaincodeId;
    }

    /**
     * @return array
     */
    public function getArgs(): array
    {
        return $this->args;
    }

    /**
     * @param array $args
     * @return void
     */
    public function setArgs(array $args): void
    {
        $this->args = $args;
    }
}
