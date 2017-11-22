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

use AmericanExpress\HyperledgerFabricClient\Serializer\BinaryStringSerializer;
use AmericanExpress\HyperledgerFabricClient\ValueObject\HashAlgorithm;
use Hyperledger\Fabric\Protos\MSP\SerializedIdentity;

final class TxIdFactory implements TxIdFactoryInterface
{
    /**
     * @var HashAlgorithm
     */
    private $hashAlgorithm;

    /**
     * @var BinaryStringSerializer
     */
    private $binaryStringSerializer;

    /**
     * @param HashAlgorithm $hashAlgorithm
     */
    public function __construct(HashAlgorithm $hashAlgorithm = null)
    {
        $this->hashAlgorithm = $hashAlgorithm ?: new HashAlgorithm();
        $this->binaryStringSerializer = new BinaryStringSerializer();
    }

    /**
     * @param SerializedIdentity $serializedIdentity
     * @param string $nonce
     * @return string
     */
    public function fromSerializedIdentity(SerializedIdentity $serializedIdentity, string $nonce): string
    {
        $noArray = $this->binaryStringSerializer->deserialize($nonce);

        $identityArray = $this->binaryStringSerializer->deserialize($serializedIdentity->serializeToString());

        $comp = \array_merge($noArray, $identityArray);

        $compString = $this->binaryStringSerializer->serialize($comp);

        return \hash((string) $this->hashAlgorithm, $compString);
    }
}
