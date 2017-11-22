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

namespace AmericanExpress\HyperledgerFabricClient;

use AmericanExpress\HyperledgerFabricClient\Options\AbstractOptions;

class ChannelContext extends AbstractOptions
{
    /**
     * @var string|null
     */
    private $host;

    /**
     * @var string|null
     */
    private $mspId;

    /**
     * @var \SplFileObject|null
     */
    private $adminCerts;

    /**
     * @var \SplFileObject|null
     */
    private $privateKey;

    /**
     * @return string|null
     */
    public function getHost(): ?string
    {
        return $this->host;
    }

    /**
     * @param string $host
     */
    public function setHost(string $host)
    {
        $this->host = $host;
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
     */
    public function setMspId(string $mspId)
    {
        $this->mspId = $mspId;
    }

    /**
     * @return \SplFileObject|null
     */
    public function getAdminCerts(): ?\SplFileObject
    {
        return $this->adminCerts;
    }

    /**
     * @param \SplFileObject $adminCerts
     */
    public function setAdminCerts(\SplFileObject $adminCerts)
    {
        $this->adminCerts = $adminCerts;
    }

    /**
     * @return \SplFileObject|null
     */
    public function getPrivateKey(): ?\SplFileObject
    {
        return $this->privateKey;
    }

    /**
     * @param \SplFileObject $privateKey
     */
    public function setPrivateKey(\SplFileObject $privateKey)
    {
        $this->privateKey = $privateKey;
    }
}
