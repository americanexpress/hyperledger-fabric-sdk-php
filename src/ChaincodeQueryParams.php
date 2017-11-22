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

class ChaincodeQueryParams extends AbstractOptions
{
    /**
     * @var string|null
     */
    private $channelId;

    /**
     * @var string|null
     */
    private $chaincodeName;

    /**
     * @var string|null
     */
    private $chaincodePath;

    /**
     * @var string|null
     */
    private $chaincodeVersion;

    /**
     * @var array
     */
    private $args = [];

    /**
     * @return string|null
     */
    public function getChannelId(): ?string
    {
        return $this->channelId;
    }

    /**
     * @param string $channelId
     */
    public function setChannelId(string $channelId)
    {
        $this->channelId = $channelId;
    }

    /**
     * @return string|null
     */
    public function getChaincodeName(): ?string
    {
        return $this->chaincodeName;
    }

    /**
     * @param string $chaincodeName
     */
    public function setChaincodeName(string $chaincodeName)
    {
        $this->chaincodeName = $chaincodeName;
    }

    /**
     * @return string|null
     */
    public function getChaincodePath(): ?string
    {
        return $this->chaincodePath;
    }

    /**
     * @param string $chaincodePath
     */
    public function setChaincodePath(string $chaincodePath)
    {
        $this->chaincodePath = $chaincodePath;
    }

    /**
     * @return string|null
     */
    public function getChaincodeVersion(): ?string
    {
        return $this->chaincodeVersion;
    }

    /**
     * @param string $chaincodeVersion
     */
    public function setChaincodeVersion(string $chaincodeVersion)
    {
        $this->chaincodeVersion = $chaincodeVersion;
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
     */
    public function setArgs(array $args)
    {
        $this->args = $args;
    }
}
