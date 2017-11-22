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

namespace AmericanExpress\HyperledgerFabricClient\Channel;

use AmericanExpress\HyperledgerFabricClient\ChannelFactory;
use AmericanExpress\HyperledgerFabricClient\ChannelInterface;
use AmericanExpress\HyperledgerFabricClient\Config\ClientConfigInterface;

final class ChannelManager implements ChannelManagerInterface
{
    /**
     * @var ClientConfigInterface
     */
    private $config;

    /**
     * @var ChannelInterface[]
     */
    private $instances = [];

    /**
     * ChannelManager constructor.
     * @param ClientConfigInterface $config
     */
    public function __construct(ClientConfigInterface $config)
    {
        $this->config = $config;
    }

    /**
     * @param string $name
     * @return ChannelInterface
     * @throws \AmericanExpress\HyperledgerFabricClient\Exception\RuntimeException
     * @throws \AmericanExpress\HyperledgerFabricClient\Exception\InvalidArgumentException
     */
    public function get(string $name): ChannelInterface
    {
        if (!\array_key_exists($name, $this->instances)) {
            $this->instances[$name] = ChannelFactory::fromConfig($name, $this->config);
        }

        return $this->instances[$name];
    }
}
