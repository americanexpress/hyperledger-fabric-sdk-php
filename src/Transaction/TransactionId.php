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

use Hyperledger\Fabric\Protos\MSP\SerializedIdentity;

class TransactionId
{
    /**
     * @var SerializedIdentity
     */
    private $serializedIdentity;

    /**
     * @var string
     */
    private $nonce;

    /**
     * @var string
     */
    private $id;

    /**
     * @param SerializedIdentity $serializedIdentity
     * @param string $nonce
     * @param string $id
     */
    public function __construct(SerializedIdentity $serializedIdentity, string $nonce, string $id)
    {
        $this->serializedIdentity = $serializedIdentity;
        $this->nonce = $nonce;
        $this->id = $id;
    }

    /**
     * @return string
     */
    public function getId(): string
    {
        return $this->id;
    }

    /**
     * @return SerializedIdentity
     */
    public function getSerializedIdentity(): SerializedIdentity
    {
        return $this->serializedIdentity;
    }

    /**
     * @return string
     */
    public function getNonce(): string
    {
        return $this->nonce;
    }
}
