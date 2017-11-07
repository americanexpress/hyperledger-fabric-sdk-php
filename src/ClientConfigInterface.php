<?php

namespace AmericanExpress\HyperledgerFabricClient;

interface ClientConfigInterface
{
    /**
     * @param string $key
     * Method to get default SDK configuration.
     * @return mixed
     */
    public function getDefault(string $key);

    /**
     * @param string[] $keys
     * @param mixed $default
     * @return mixed
     */
    public function getIn(array $keys = [], $default = null);
}
