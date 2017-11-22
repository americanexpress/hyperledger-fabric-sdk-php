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

use AmericanExpress\HyperledgerFabricClient\Options\AbstractOptions;

class PeerOptions extends AbstractOptions implements PeerOptionsInterface
{
    /**
     * @var string|null
     */
    private $name;

    /**
     * @var string|null
     */
    private $requests;

    /**
     * @var string|null
     */
    private $events;

    /**
     * @var string|null
     */
    private $serverHostname;

    /**
     * @var string|null
     */
    private $tlsCaCerts;

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
    public function getRequests(): ?string
    {
        return $this->requests;
    }

    /**
     * @param string $requests
     */
    public function setRequests(string $requests)
    {
        $this->requests = $requests;
    }

    /**
     * @return string|null
     */
    public function getEvents(): ?string
    {
        return $this->events;
    }

    /**
     * @param string $events
     */
    public function setEvents(string $events)
    {
        $this->events = $events;
    }

    /**
     * @return string|null
     */
    public function getServerHostname(): ?string
    {
        return $this->serverHostname;
    }

    /**
     * @param string $serverHostname
     */
    public function setServerHostname(string $serverHostname)
    {
        $this->serverHostname = $serverHostname;
    }

    /**
     * @return string|null
     */
    public function getTlsCaCerts(): ?string
    {
        return $this->tlsCaCerts;
    }

    /**
     * @param string $tlsCaCerts
     */
    public function setTlsCaCerts(string $tlsCaCerts)
    {
        $this->tlsCaCerts = $tlsCaCerts;
    }
}
