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

class TransactionContext
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
    private $txId;

    /**
     * @var int
     */
    private $epoch = 0;

    /**
     * @param SerializedIdentity $serializedIdentity
     * @param string $nonce
     * @param string $txId
     * @param int $epoch
     */
    public function __construct(SerializedIdentity $serializedIdentity, string $nonce, string $txId, int $epoch = 0)
    {
        $this->serializedIdentity = $serializedIdentity;
        $this->nonce = $nonce;
        $this->txId = $txId;
        $this->epoch = $epoch;
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

    /**
     * @return string
     */
    public function getTxId(): string
    {
        return $this->txId;
    }

    /**
     * @return int
     */
    public function getEpoch(): int
    {
        return $this->epoch;
    }
}
