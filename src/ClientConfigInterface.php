<?php

namespace AmericanExpress\HyperledgerFabricClient;

interface ClientConfigInterface
{
    /**
     * @param string[] $keys
     * @param mixed $default
     * @return mixed
     */
    public function getIn(array $keys = [], $default = null);
}
